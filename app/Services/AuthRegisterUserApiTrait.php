<?php

namespace App\Services;

use Validator;
use App\Enums\Roles;
use App\Models\User;
use Illuminate\Http\Request;
use App\Enums\ProfileStatuses;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

/**
 * Trait AuthRegisterUserApiTrait
 *
 * @package App\Services
 */
trait AuthRegisterUserApiTrait
{
    private static $client = 'registerClient';

    /**
     * @param Request $request
     * @param string $params
     *
     * @return mixed
     */
    public function validateUser(Request $request, string $params): mixed
    {

       return Validator::make($request->all(),
            [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|same:password',
                'phone_number' => 'required',
                'user_image_path' => 'nullable|image|mimes:jpeg,jpg,png,gif',
                $params => 'required|string',
                'suite' => 'nullable|string',
            ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function validateCandidate(Request $request)
    {
        $positions = $request->input('positions');
        $rules = [
            'transportation' => 'required|integer',
            'desired_positions' => 'required|array',
            'city' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'required|string',
            'candidateLanguages' => 'required|array|exists:languages,id',
            'softwares' => 'required|array|exists:softwares,id',
            'cv_path' => 'required|array',
            'cv_path.*' => 'required|mimes:doc,docx,pdf',
            'certificates' => 'required|array',
            'certificates.*' => 'required|mimes:doc,docx,pdf',
            'suite' => 'nullable|string',
            'school' => 'required|string',
            'positions' => 'required|array'
        ];
        if (isset($positions) && count(array_intersect($positions, [1, 2, 4, 5, 7, 8])) > 0) {
            $rules['year_graduated'] = 'required';
        }
        if (isset($positions) && in_array(1, $positions)) {
            $rules['registration'] = 'required';
            $rules['expiry_date'] = 'required';
        }
        $validator = Validator::make($request->all(), $rules);
        return $validator;
    }

    /**
     * @param Request $request
     * @param null $param
     * @param int $roleId
     *
     * @return User
     */
    public function storeUser(Request $request, $param = null, int $roleId): User
    {
        $user = new User();

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->administration_id = 1;
        $user->phone_number = $request->phone_number;
        if ($param) $user->$param = $param;
        $user->is_api_user = true;
        $user->role_id = $roleId;
        $user->profile_status_id = ProfileStatuses::PENDING_REVIEW;
        $user->suite = $request->suite;
        $user->save();
        if ($request->has('user_image_path')) {
            $user->uploadImage();
            $user->save();
        }

        $user->profileStatuses()->create([
            'user_id' => $user->id,
            'status' => ProfileStatuses::PENDING_REVIEW
        ]);

        return $user;
    }

}
