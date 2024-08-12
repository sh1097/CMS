<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
# Models
use App\Models\User;
use App\Models\UserSubscription;
use DB;
use File;
use Validator;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Mail\UserSendMail;
# Traits
use App\Http\Traits\StatusTrait;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    use StatusTrait;
    # Variable to Bind Model
    protected $user;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        User           $user
    )
    {
        $this->user  = $user;
    }

    /** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    {
       
       # Validate request data
        $validator = Validator::make($request->all(),[
                  'name'     => 'required|string|max:255',
                  'email'    => ['required','max:255','regex:/^\w+[-\.\w]*@(?!(?:)\.com$)\w+[-\.\w]*?\.\w{2,4}$/'],
                  'mobile'              => 'max:15',
                  'password'            => 'required|min:6',
                  // 'address'             => 'required',
                  'country_name'        => 'max:200',
                  'state_name'          => 'max:200',
                  'city_name'           => 'max:200',
                  'pin_code'            => 'max:9',
                  'device_type'         => 'required',
                  'device_token'        => 'required',
                  
              ]);

        # If validator fails return response
        if ($validator->fails()) { 
            return response()->json([
                                      'code' => (string)$this->failedStatus,
                                      'message'=>$validator->errors()->first() 
                                  ]);            
        }


        try {

            # check user already Exist
            $checkUser = $this->user->where('email', $request->email)
                                    ->where('delete_status',0)
                                    ->first();

            if($checkUser)
            {

                return response()->json([
                                      'code' => (string)$this->failedStatus,
                                      'message'=> 'This Email Already Exist!'
                                  ]);
            }

            $checkDeleteUser = $this->user->where('email', $request->email)
                                    ->where('delete_status',1)
                                    ->first();
            if($checkDeleteUser)
            {
                $trial_expired_date = Carbon::now()->addDays(3);
                $ebook_expire = date('Y-m-d',strtotime($trial_expired_date));

                $arrayData = [
                            'name'                => $request->name??null,
                            'email'               => $request->email??null,
                            'mobile'              => $request->mobile??null,
                            'password'            => Hash::make($request->password)??null,
                            'password_text'       => $request->password??null,
                            'address'             => $request->address??null,
                            'country_name'        => $request->country_name??null,
                            'state_name'          => $request->state_name??null,
                            'city_name'           => $request->city_name??null,
                            'pin_code'            => $request->pin_code??null,
                            'enrollement_date'    => date('Y-m-d'),
                            'trial_expired_date'  => $ebook_expire,
                            'device_type'         => $request->device_type??null,
                            'device_token'        => $request->device_token?? null,
                            'delete_status'       => 0,
                         ];

                # Create user Model
                $Updateuser =  $checkDeleteUser->update($arrayData);
                $user = $this->user->where('id',$checkDeleteUser->id)->first();

                $to         = 'support@mahjcon.com';
                $full_name  = 'Mahjpedia';
                $msg        = 'You have a new user registration.Below are details: User Name: '.$user->name.' and User Email: '.$user->email;
                $subject    = 'New Account Created on Mahjpedia App.';
                $arrayData2 = [  
                'name'              => $full_name ?? '',
                'message'           => $msg ??'',
                'subject'           => $subject ??'',

                ];
                Mail::to($to)->send(new UserSendMail($user, $subject, $arrayData2));

                $data = [
                      'name' => $user->name??'', 
                      'email' => $user->email??'',
                    ];
                # return response
                return response()->json([
                'code'      => (string)$this->successStatus, 
                'message'   => 'Register successfully',
                'data'      => $data??[]
                ]);
                
            }else{

                $trial_expired_date = Carbon::now()->addDays(3);
                $ebook_expire = date('Y-m-d',strtotime($trial_expired_date));

                $arrayData = [
                            'name'                => $request->name??null,
                            'email'               => $request->email??null,
                            'mobile'              => $request->mobile??null,
                            'password'            => Hash::make($request->password)??null,
                            'password_text'       => $request->password??null,
                            'address'             => $request->address??null,
                            'country_name'        => $request->country_name??null,
                            'state_name'          => $request->state_name??null,
                            'city_name'           => $request->city_name??null,
                            'pin_code'            => $request->pin_code??null,
                            'enrollement_date'    => date('Y-m-d'),
                            'trial_expired_date'  => $ebook_expire,
                            'device_type'         => $request->device_type??null,
                            'device_token'        => $request->device_token?? null,
                            'delete_status'       => 0,
                         ];

                # Create user Model
                $user =  $this->user->create($arrayData);

                $to         = 'support@mahjcon.com';
                $full_name  = 'Mahjpedia';
                $msg        = 'You have a new user registration.Below are details: User Name: '.$user->name.' and User Email: '.$user->email;
                $subject    = 'New Account Created on Mahjpedia App.';
                $arrayData2 = [  
                'name'              => $full_name ?? '',
                'message'           => $msg ??'',
                'subject'           => $subject ??'',

                ];
                Mail::to($to)->send(new UserSendMail($user, $subject, $arrayData2));

                $data = [
                      'name' => $user->name??'', 
                      'email' => $user->email??'',
                    ];
                # return response
                return response()->json([
                'code'      => (string)$this->successStatus, 
                'message'   => 'Register successfully',
                'data'      => $data??[]
                ]);

            }

            
        } catch (Exception $e) {

            return response()->json([
                                      'code' => (string)$this->failedStatus,
                                      'message'=> 'Something Went Worng.'
                                  ]);  
            
        }

         

    }

    public function login(Request $request)
    {
        # Validate request data
        $validator = Validator::make($request->all(),[

            'email'    => ['required','max:255','regex:/^\w+[-\.\w]*@(?!(?:)\.com$)\w+[-\.\w]*?\.\w{2,4}$/'],
            'password' => 'required|min:6',                  
        ]);

        # If validator fails return response
        if ($validator->fails()) { 
            return response()->json([
                                      'code' => (string)$this->failedStatus,
                                      'message'=>$validator->errors()->first() 
                                  ]);            
        }

        try {

               $user = $this->user->where('email', $request->email)->first();

               if(!$user)
               {
                   return response()->json([
                                    'code'    => (string)$this->failedStatus,
                                    'message' =>'Email not Exist!'
                                ]);
               }else
               {
                    #check Delete user
                    if($user->delete_status == 1){

                        return response()->json([
                                    'code'    => (string)$this->failedStatus,
                                    'message' =>'Your Account Deleted Please Register Again'
                                ]);
                    }
                    # otherwise
                    $credentials = $request->only('email', 'password');

                    if (!Auth::attempt($credentials))
                    {    
                            return response()->json([
                                'code'    => (string)$this->failedStatus,
                                'message' =>'Wrong Email or Password'
                            ]);
                    }else
                    {
                        $token = $this->generateToken();
                         # Update user token
                        $user->update([
                            'api_token'   => $token,
                            'device_type' => $request->device_type ?? '',
                            'device_token'=> $request->device_token ?? ''
                        ]);

                        
                        $currentDate = date('Y-m-d',strtotime(Carbon::now()));

                        $getSubscriptin = UserSubscription::where('user_id',$user->id)
                                                          ->where('subscription_end_date','>',$currentDate)
                                                          ->first();
                         
                        if($getSubscriptin)
                        {
                           $subscription_exit = 1;
                           $data1 = [
                                     'subscription_name' => $getSubscriptin->subscription_name,
                                     'subscription_type' => $getSubscriptin->subscription_type,
                                     'subscription_price' => $getSubscriptin->subscription_price,
                                     'subscription_start_date' => $getSubscriptin->subscription_start_date,
                                     'subscription_end_date' => $getSubscriptin->subscription_end_date,
                                    ]; 
                          
                        }else{

                           $subscription_exit = 0;
                        }

                        $rating_date = date('Y-m-d',strtotime(Carbon::parse($user->enrollement_date)->addDays(14)));
                        
                        
                        if(strtotime($rating_date) < strtotime($currentDate) && $user->rating == 2 )
                        {
                            $rating_popup = 1;
                        }else{
                             $rating_popup = 0;
                        }

                        

                        # Set the Data
                        $data = [
                           'id'                 => (string)$user->id,
                           'name'               => (string)$user->name ?? '',
                           'email'              => (string)$user->email ?? '',
                           'mobile'             => (string)$user->mobile ?? '',
                           'address'            => (string)$user->address ?? '',
                           'country_name'       => (string)$user->country_name ?? '',
                           'state_name'         => (string)$user->state_name ?? '',
                           'city_name'          => (string)$user->city_name ?? '',
                           'pin_code'           => (string)$user->pin_code ?? '',
                           'enrollement_date'   => (string)$user->enrollement_date ?? '',
                           'trial_expired_date' => (string)$user->trial_expired_date ??null,
                           'api_token'          => (string)$token ?? '',
                           'subscription_exit'  => (string)$subscription_exit ,
                           'subscription_details'=>$data1??[],
                           'rating'             => (string)$user->rating ?? '',
                           'rating_popup_show'  => (string)$rating_popup ?? '',
                       ];

                        

                        # return response
                            return response()->json([
                                'code'      => (string)$this->successStatus, 
                                'message'   => 'Login Successfully !',
                                'data'      => $data
                            ]);


                    }
               }
            
        } catch (Exception $e) {

            return response()->json([
                                      'code' => (string)$this->failedStatus,
                                      'message'=> 'Something Went Worng.'
                                  ]);
            
        }
    }


    /** 
     * Get Profile api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function getProfile(Request $request) 
    { 

        # Validate request data
        $validator = Validator::make($request->all(),[
                  'id'     => 'required|numeric'
              ]);

        # If validator fails return response
        if ($validator->fails()) { 
            return response()->json([
                                       'code'    => (string)$this->failedStatus,            
                                       'message' =>$validator->errors()->first(),
                                    ]); 
        }

        try {
             
            # check user already Exist on that id
             $user = $this->user->where('id', $request->id)->first();
            
            # return response if user already exist on requested Email/Mobile
            if($user) 
            {
                    $currentDate = date('Y-m-d',strtotime(Carbon::now()));
                    $getSubscriptin = UserSubscription::where('user_id',$user->id)
                                                      ->where('subscription_end_date','>',$currentDate)
                                                      ->first();

                    if($getSubscriptin)
                    {
                        $subscription_exit = 1;
                        $data1 = [
                        'subscription_name' => $getSubscriptin->subscription_name,
                        'subscription_type' => $getSubscriptin->subscription_type,
                        'subscription_price' => $getSubscriptin->subscription_price,
                        'subscription_start_date' => $getSubscriptin->subscription_start_date,
                        'subscription_end_date' => $getSubscriptin->subscription_end_date,
                        ]; 

                    }else{

                       $subscription_exit = 0;
                    }
                    # Set the Data
                    $data = [
                         'id'                  => (string)$user->id,
                         'name'                => (string)$user->name ?? '',
                         'email'               => (string)$user->email ?? '',
                         'mobile'              => (string)$user->mobile ?? '',
                         'address'             => (string)$user->address ?? '',
                         'country_name'        => (string)$user->country_name ?? '',
                         'state_name'          => (string)$user->state_name ?? '',
                         'city_name'           => (string)$user->city_name ?? '',
                         'pin_code'            => (string)$user->pin_code ?? '',
                         'enrollement_date'    => (string)$user->enrollement_date ?? '',
                         'trial_expired_date'  => (string)$user->trial_expired_date ?? '',
                         'profile_img'         => (string)$user->profile_img ?? '',
                         'subscription_exit'  => (string)$subscription_exit ,
                         'subscription_details'=>$data1??[],
                         'rating'             => (string)$user->rating ?? '',
                        ];


                    # return response
                    return response()->json([
                        'code'      => (string)$this->successStatus, 
                        'message'   => 'User profile data',
                        'data'      =>  $data
                     ]);
                 
            } else {
               # return response
                return response()->json([
                    'code'      => (string)$this->failedStatus, 
                    'message'   => 'User not Found on User Id.',
                    'data'      => []
                 ]); 
            }
        } catch (Exception $e) {
            # return response
                  return response()->json([
                      'code'      => (string)$this->failedStatus, 
                      'message'   => 'Something Went Worng.'
                   ]);
        }
    }

    /** 
     * Update Profile api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function updateProfile(Request $request) 
    { 

        # Validate request data
        $validator = Validator::make($request->all(),[
                  'id'         => 'required|numeric',
                  'name'       => 'required|string',
                  'country_name'   => 'max:200',
                  'state_name'     => 'max:200',
                  'city_name'      => 'max:200',
                  'pin_code'       => 'max:9',
              ]);

        # If validator fails return response
        if ($validator->fails()) { 
            return response()->json([
                                      'code' => (string)$this->failedStatus,
                                      'message'=>$validator->errors()->first(), 
                                    ]);            
        }

        try {
             

            # check user already Exist on that id
             $user = $this->user->where('id', $request->id)->first();
            
            # return response if user already exist on requested Email/Mobile
            if($user) {

                        
                         #upload profileImage
                        if ($request->hasfile('profileImage'))
                        {
                            $file = $request->file('profileImage');
                            $extension = $file->getClientOriginalExtension(); // getting image extension
                            $filename =((string)(microtime(true)*10000)).'.'.$extension;
                            File::delete(public_path($user->profile_img));
                            $file->move(public_path('images/user/'), $filename);
                            $profileImage='images/user/'.$filename;
                        }else{
                            $profileImage=  $user->profile_img;
                        }

                        # Set the success message after user update 
                        $data = [
                                 'name'              => $request->name ?? '',
                                 'mobile'            => $request->mobile ?? null,
                                 'address'           => $request->address ?? null,
                                 'country_name'      => $request->country_name ?? null,
                                 'state_name'        => $request->state_name ?? null,
                                 'city_name'         => $request->city_name ?? null,
                                 'pin_code'          => $request->pin_code ?? null,
                                 'profile_img'       => $profileImage ?? null,
                                ];

                        # Create/update profile
                        $user->update($data);
                      
                        # Set the Data
                        $data = [
                         'id'                  => (string)$user->id,
                         'name'                => (string)$user->name ?? '',
                         'email'               => (string)$user->email ?? '',
                         'mobile'              => (string)$user->mobile ?? '',
                         'address'             => (string)$user->address ?? '',
                         'country_name'        => (string)$user->country_name ?? '',
                         'state_name'          => (string)$user->state_name ?? '',
                         'city_name'           => (string)$user->city_name ?? '',
                         'pin_code'            => (string)$user->pin_code ?? '',
                         'enrollement_date'    => (string)$user->enrollement_date ?? '',
                         'trial_expired_date'  => (string)$user->trial_expired_date ?? '',
                         'profile_img'         => (string)$user->profile_img ?? '',
                        ];

                    # return response
                    return response()->json([
                        'code'      => (string)$this->successStatus, 
                        'message'   => 'Profile update successfully',
                        'data'      =>  $data
                     ]);
                 
            } else {
               # return response
                return response()->json([
                    'code'      => (string)$this->failedStatus, 
                    'message'   => 'User not Found on User Id.',
                    'data'      => []
                 ]); 
            }
        } catch (Exception $e) {
            # return response
                  return response()->json([
                      'code'      => (string)$this->failedStatus, 
                      'message'   => 'Something Went Worng.'
                   ]);
        }
    }
    /** 
     * Update Profile api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function changePassword(Request $request) 
    { 

        # Validate request data
        $validator = Validator::make($request->all(),[
                  'id'         => 'required|numeric',
                  'password'   => 'required|min:6',
                  'password_confirmation' => 'required_with:password|same:password|min:6',
              ]);

        # If validator fails return response
        if ($validator->fails()) { 
            return response()->json([
                                      'code' => (string)$this->failedStatus,
                                      'message'=>$validator->errors()->first(), 
                                    ]);            
        }

        try {
             

            # check user already Exist on that id
             $user = $this->user->where('id', $request->id)->first();
            
            # return response if user already exist on requested Email/Mobile
            if($user) {


                         

                        # Set the success message after user update 
                        $arrayData = [
                            'password'            => Hash::make($request->password),
                            'password_text'       => $request->password,
                        ];

                        # Create/update profile
                        $user->update($arrayData);

                    # return response
                    return response()->json([
                        'code'      => (string)$this->successStatus, 
                        'message'   => 'Password Update successfully',
                        'data'      =>  ''
                     ]);
                 
            } else {
               # return response
                return response()->json([
                    'code'      => (string)$this->failedStatus, 
                    'message'   => 'User not Found on User Id.',
                    'data'      => []
                 ]); 
            }
        } catch (Exception $e) {
            # return response
                  return response()->json([
                      'code'      => (string)$this->failedStatus, 
                      'message'   => 'Something Went Worng.'
                   ]);
        }
    }


    public function ratingSave(Request $request) 
    { 

        # Validate request data
        $validator = Validator::make($request->all(),[
                  'id'         => 'required|numeric',
                  'rating'     => 'required|numeric',
              ]);

        # If validator fails return response
        if ($validator->fails()) { 
            return response()->json([
                                      'code' => (string)$this->failedStatus,
                                      'message'=>$validator->errors()->first(), 
                                    ]);            
        }

        try {
             

            # check user already Exist on that id
             $user = $this->user->where('id', $request->id)->first();
            
            # return response if user already exist on requested Email/Mobile
            if($user) 
            {

                    # Set the success message after user update 
                    $arrayData = [
                        'rating'  => $request->rating,
                    ];

                    # Create/update profile
                    $user->update($arrayData);

                    # return response
                    return response()->json([
                        'code'      => (string)$this->successStatus, 
                        'message'   => 'Rating Save successfully',
                        'data'      =>  ''
                     ]);
                 
            } else {
               # return response
                return response()->json([
                    'code'      => (string)$this->failedStatus, 
                    'message'   => 'User not Found on User Id.',
                    'data'      => []
                 ]); 
            }
        } catch (Exception $e) {
            # return response
                  return response()->json([
                      'code'      => (string)$this->failedStatus, 
                      'message'   => 'Something Went Worng.'
                   ]);
        }
    }

    public function checkToken(Request $request) 
    { 

        # Validate request data
        $validator = Validator::make($request->all(),[
                  'id'         => 'required|numeric',
                  'token'      => 'required',
              ]);

        # If validator fails return response
        if ($validator->fails()) { 
            return response()->json([
                                      'code' => (string)$this->failedStatus,
                                      'message'=>$validator->errors()->first(), 
                                    ]);            
        }

        try {
             

            # check user already Exist on that id
             $user = $this->user->where('id', $request->id)->first();
            
            # return response if user already exist on requested Email/Mobile
            if($user) 
            {
                    
                    # get the user Token
                    $apiToken = $user->api_token;
                    # get Request Token
                    $authorizationToken = $request->token;

                    if($apiToken == $authorizationToken)
                    {
                        # return response
                        return response()->json([
                            'code'      => (string)$this->successStatus, 
                            'message'   => 'Token is valid',
                         ]);
                    }

                    # return response
                    return response()->json([
                        'code'      => (string)$this->failedStatus, 
                        'message'   => 'Unauthenticated User.',
                     ]);
                 
            } else {
               # return response
                return response()->json([
                    'code'      => (string)$this->failedStatus, 
                    'message'   => 'User not Found on User Id.',
                 ]); 
            }
        } catch (Exception $e) {
            # return response
                  return response()->json([
                      'code'      => (string)$this->failedStatus, 
                      'message'   => 'Something Went Worng.'
                   ]);
        }
    }



    /**
     * function to generate the Token
     * 
     * @return Token
     */
    public function generateToken()
    {
        # Set the token 
        $token = Str::random(60);

        # Hash Token
        $hashToken = hash('sha256', $token);

        # return the Hash Token 
        return $hashToken;
    }

    public function forgotPassword(Request $request)
    {
         # Validate request data
        $validator = Validator::make($request->all(),[

            'email'    => ['required','max:255','regex:/^\w+[-\.\w]*@(?!(?:)\.com$)\w+[-\.\w]*?\.\w{2,4}$/'],
                             
        ]);

        # If validator fails return response
        if ($validator->fails()) { 
            return response()->json([
                                      'code' => (string)$this->failedStatus,
                                      'message'=>$validator->errors()->first() 
                                  ]);            
        }

        try {

               $user = $this->user->where('email', $request->email)->where('delete_status',0)->first();

               if(!$user)
               {
                   return response()->json([
                                    'code'    => (string)$this->failedStatus,
                                    'message' =>'Email not Exist!'
                                ]);
               }else
               {

                    #check Delete user
                    if($user->delete_status == 1)
                    {

                        return response()->json([
                                    'code'    => (string)$this->failedStatus,
                                    'message' =>'Your Account Deleted Please Register Again'
                                ]);
                    }

                    $to         = $user->email;
                    $full_name  = $user->name;
                    $msg        = 'Thank you for showing your interest on Mahjpedia.Your account username:- '.$user->email.' and password:- '.$user->password_text.' You can use this login details and change the password.';
                    $subject    = 'Forgot Password!';
                    $arrayData2 = [  
                    'name'              => $full_name ?? '',
                    'message'           => $msg ??'',
                    'subject'           => $subject ??'',

                    ];
                    Mail::to($to)->send(new UserSendMail($user, $subject, $arrayData2));

                    # return response
                    return response()->json([
                        'code'      => (string)$this->successStatus, 
                        'message'   => 'Password Send on Email successfully',
                        'data'      =>  ''
                     ]);
                    
               }
            
        } catch (Exception $e) {

            return response()->json([
                                      'code' => (string)$this->failedStatus,
                                      'message'=> 'Something Went Worng.'
                                  ]);
            
        }
    }


    public function subscriptionCreate(Request $request)
    {
        # Validate request data
        $validator = Validator::make($request->all(),[
                  'user_id'               => 'required|numeric',
                  'subscription_type'     => 'required|string',
                  'subscription_price'    => 'required',
                  'payment_date'          => 'required',
                  'payment_id'            => 'required',
                  'payment_detail'        => 'required',
                 
                  
              ]);

        # If validator fails return response
        if ($validator->fails()) { 
            return response()->json([
                                      'code' => (string)$this->failedStatus,
                                      'message'=>$validator->errors()->first() 
                                  ]);            
        }

        try {

            if($request->subscription_type == 'year'){
                $subscription_name = "Yearly";
             $trial_expired_date = Carbon::parse($request->payment_date)->addYears(1);

            }else{
                $subscription_name = "Lifetime";
                $trial_expired_date = Carbon::parse($request->payment_date)->addYears(200);
            }

             $ebook_expire = date('Y-m-d',strtotime($trial_expired_date));
             $subscription_start_date = date('Y-m-d',strtotime($request->payment_date));
               
            $arrayData = [
                            'user_id' => $request->user_id,
                            'subscription_name'       => $subscription_name,
                            'subscription_type'       => $request->subscription_type,
                            'subscription_price'      => $request->subscription_price,
                            'subscription_start_date' => $subscription_start_date ,
                            'subscription_end_date'   => $ebook_expire,
                            'payment_id'              => $request->payment_id??null,
                            'payment_detail'        => json_encode($request->payment_detail)??null,
                         ];
            $subscriptionCreate = UserSubscription::create($arrayData);
            $user               = $this->user->where('id', $request->user_id)->first();
            $arrayData2         = [
                                    'trial_expired_date'  => null,
                                  ];

            # Create/update profile
            $user->update($arrayData2);

            if($request->subscription_type == 'year'){
                  $date = $subscriptionCreate->subscription_end_date;

            }else{

                $date = "Lifetime";
            }


            $to         = 'support@mahjcon.com';
            $full_name  = 'Mahjpedia';
            $msg        = 'One of your users has purchased a subscription plan with user name is '.$user->name.' and email is '.$user->email.'. We’re happy to confirm the details of your subscription. Subscription Name '.$subscriptionCreate->subscription_name.' ,Subscription Start Date '.$subscriptionCreate->subscription_start_date.' and Subscription End Date '.$date.'.';
            $subject    = 'You have Received new Subscription!';
            
            $arrayData2 = [  
            'name'              => $full_name ?? '',
            'message'           => $msg ??'',
            'subject'           => $subject ??'',

            ];
            Mail::to($to)->send(new UserSendMail($user, $subject, $arrayData2));


            # return response
            return response()->json([
                'code'      => (string)$this->successStatus, 
                'message'   => 'Subscription Created successfully',
                'data'      => []
             ]);
            
        } catch (Exception $e) {

            return response()->json([
                                      'code' => (string)$this->failedStatus,
                                      'message'=> 'Something Went Worng.'
                                  ]);
            
        }


    }

    public function subscriptionHistory(Request $request)
    {
        # Validate request data
        $validator = Validator::make($request->all(),[
                  'user_id' => 'required|numeric'
              ]);

        # If validator fails return response
        if ($validator->fails()) { 
            return response()->json([
                                       'code'    => (string)$this->failedStatus,            
                                       'message' =>$validator->errors()->first(),
                                    ]); 
        }

        try {
             
            # check user already Exist on that id
             $user = $this->user->where('id', $request->user_id)->first();
            
            # return response if user already exist on requested Email/Mobile
            if($user) 
            {
                    $getSubscriptions = UserSubscription::where('user_id',$user->id)->orderby('id','desc')->get();

                    if(!$getSubscriptions){
                        # return response
                        return response()->json([
                            'code'      => (string)$this->successStatus, 
                            'message'   => 'No Data Found!',
                            'data'      =>  []
                        ]);
                    }
                     
                     $completeOrderArray = [];
                    foreach ($getSubscriptions as $key => $getSubscription) {

                            if($getSubscription->subscription_type == 'year')
                            {
                               $date = $getSubscription->subscription_end_date;

                            }else{

                               $date = "Lifetime";
                            }
                        $data = [
                           'subscription_name'       => $getSubscription->subscription_name,
                           'subscription_type'       => $getSubscription->subscription_type,
                           'subscription_price'      => $getSubscription->subscription_price,
                           'subscription_start_date' => $getSubscription->subscription_start_date,
                           'subscription_end_date'   => $date,
                           'created_date'            => $getSubscription->created_at,
                        ];

                        array_push($completeOrderArray, $data);
                    }

                     
                    # return response
                    return response()->json([
                        'code'      => (string)$this->successStatus, 
                        'message'   => 'Payment History data',
                        'data'      =>  $completeOrderArray??[]
                     ]);
                 
            } else {
               # return response
                return response()->json([
                    'code'      => (string)$this->failedStatus, 
                    'message'   => 'User not Found on User Id.',
                    'data'      => []
                 ]); 
            }
        } catch (Exception $e) {
            # return response
                  return response()->json([
                      'code'      => (string)$this->failedStatus, 
                      'message'   => 'Something Went Worng.'
                   ]);
        }
    }


    public function deleteUserAccount(Request $request) 
    { 

        # Validate request data
        $validator = Validator::make($request->all(),[
            'user_id'     => 'required|numeric',

        ]);

        # If validator fails return response
        if ($validator->fails()) { 
            return response()->json(['message'=>$validator->errors(), 'code' => (string)$this->failedStatus]);            
        }

        try {
            DB::beginTransaction();
            $user = $this->user->where('id', $request->user_id)->first();
            # return response if user already exist on requested Email/Mobile
            if($user) {

                # Set the delete_status
                $data = [
                    'delete_status' => 1,
                    'api_token'     => null
                ];

                # Create/update profile
                $user->update($data);
                DB::commit();

                 # return response
                return response()->json([
                    'code'      => (string)$this->successStatus, 
                    'message'   => 'Account Deleted successfully',
                    'data'      =>  ''
                ]);

            } else {
                     # return response
                    return response()->json([
                        'code'      => (string)$this->failedStatus, 
                        'message'   => 'User not Found on User Id.',
                        'data'      => []
                    ]); 
            }
        } catch (Exception $e) {
            # return response
            return response()->json([
                'code'      => (string)$this->failedStatus, 
                'message'   => 'Something Went Worng.'
            ]);
        }
    }

     public function checkUserAccount(Request $request)  
    { 

        # Validate request data
        $validator = Validator::make($request->all(),[
                  'email'     => 'required',
              ]);

        # If validator fails return response
        if ($validator->fails()) { 
            return response()->json(['message'=>$validator->errors()->first(), 'code' => (string)$this->failedStatus]);            
        }

        try {
             DB::beginTransaction();

             # check user already Exist on that Email
             $users = $this->user->where('email', $request->email)->get();
             # return response if user already exist on requested Email/Mobile
            if($users->isNotEmpty()) 
            {

                # fetch the First user
                $user = $users->first();
                if($user->delete_status == 1)
                {
                  # return response
                        return response()->json([
                            'code'      => (string)$this->successStatus, 
                            'message'   => 'Your Account Deleted. You want to register or not!',
                            'delete_status' => 1
                         ]);
                }else
                {
                      
                        # return response
                        return response()->json([
                            'code'      => (string)$this->successStatus, 
                            'message'   => 'Your Account not Deleted',
                            'delete_status' => 0
                        ]);  
                }

            }

            # return response
            return response()->json([
                'code'      => (string)$this->failedStatus, 
                'message'   => 'User Not Found',
                'data'      => ''
            ]);

             

        } catch (Exception $e) {
            # return response
                  return response()->json([
                      'code'      => (string)$this->failedStatus, 
                      'message'   => 'Something Went Worng.'
                   ]);
        }
    }



}
