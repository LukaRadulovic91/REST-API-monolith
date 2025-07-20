<?php

namespace App\Http\Controllers\API;

use DB;
use Validator;
use JWTAuth;
use Carbon\Carbon;
use PHPOpenSourceSaver\JWTAuth\Support\CustomClaims;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use App\Enums\Roles;
use App\Services\Clients\ClientService;
use App\Services\CreateProfileService;
use App\Services\AuthRegisterUserApiTrait;
use App\Services\FormattedResponsesTrait;
use App\Services\User\UserService;
use App\Http\Resources\Mobile\ClientResource;
use App\Http\Requests\Client\CreateClientRequest;
use App\Http\Controllers\Controller;
use function PHPUnit\Framework\isNull;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers\API
 */
class AuthController extends Controller
{
    use AuthRegisterUserApiTrait, FormattedResponsesTrait, CustomClaims;

    /** @var bool */
    public $token = true;

    /**
     * @var CreateProfileService
     */
    private CreateProfileService $createProfileService;

    /**
     * @param CreateProfileService $createProfileService
     */
    public function __construct(CreateProfileService $createProfileService)
    {
        $this->createProfileService = $createProfileService;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remeber_me' => 'nullable|bool',
            'token' => 'nullable|string'
        ]);
        $credentials = $request->only('email', 'password');

        $ttl = $request->has('remember_me') ? env('JWT_REMEMBER_TTL') : config('jwt.ttl');
        $token = JWTAuth::customClaims(['exp' => now()->addMinutes($ttl)->timestamp])->attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        if ($request->has('token') && $request->token !== null)
        {
            $user->expoTokens()->updateOrInsert(
                [ 'owner_type' => User::class, 'owner_id' => $user->id ],
                [
                    'value' => $request->has('token') ? $request->token : '',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            );
        }

        if (is_null($user->email_verified_at))
        {
            return response()->json([
                'success' => false,
                'message' => 'Email is not verified yet!'
            ], JsonResponse::HTTP_NOT_ACCEPTABLE);
        }

        if ($user->role_id === Roles::CLIENT &&  is_null($user->client->dentist_name)) {
            return response()->json([
                'success' => false,
                'message' => 'Create office profile first!',
                'user_id' => $user->id
            ], JsonResponse::HTTP_NOT_ACCEPTABLE);
        }

        return app(UserService::class)->checkUserLoginStatus($user, $token);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function registerClient(Request $request): JsonResponse
    {
        $validator = $this->validateUser($request, 'title');

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
          $user =  DB::transaction(function () use ($request) {
                $user = $this->storeUser($request, null, Roles::CLIENT);

                $this->createProfileService->storeClient($user, $request->all());

                return $user;
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        event(new Registered($user));

        return $this->okStatusResponse($user);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function registerCandidate(Request $request): JsonResponse
    {
        $candidateValidator = $this->validateCandidate($request);
        $validator = $this->validateUser($request, 'address');

        if ($candidateValidator->fails() || $validator->fails()) {
            return response()
                ->json(array_merge(
                    $candidateValidator->errors()->toArray(),
                    $validator->errors()->toArray()
                ), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $user = DB::transaction(function () use ($request) {
                $user = $this->storeUser($request, null, Roles::CANDIDATE);
                $this->createProfileService->storeCandidate($user, $request);

                return $user;
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        event(new Registered($user));

        return $this->okStatusResponse($user);
    }

    /**
     * @param CreateClientRequest $request
     * @param User $user
     * @param ClientService $clientService
     *
     * @return JsonResponse
     */
    public function createOfficeProfile(
        CreateClientRequest $request,
        User $user,
        ClientService $clientService
    ): JsonResponse
    {
        $client = $clientService->createOfficeProfile($request, $user);

        return (new ClientResource($client))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        $this->defaultOkStatusResponse('User logged out successfully');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function checkUniqueEmail(Request $request): JsonResponse
    {
        $email = $request->input('email');

        $emailExists = User::where('email', $email)->first();

        if ($emailExists) {
            return response()->json(['success' => false, 'message' => 'Email already exists.']);
        }

        return response()->json(['success' => true, 'message' => 'Email is available.']);
    }

    /**
     * @param Request $request
     * @param UserService $userService
     *
     * @return JsonResponse
     */
    public function updatePassword(Request $request, UserService $userService)
    {
        $validator = $userService->validateDataForUpdatePassword($request);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->toArray()]);
        }

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json(['error' => 'The current password is incorrect.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $userService->updatePassword($request->new_password);

        return response()->json(['success' => true, 'message' => 'The password is successfully changed.']);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    private function handleTokenMissmatch(Request $request)
    {
        if (!$this->token) return $this->login($request);
    }
}
