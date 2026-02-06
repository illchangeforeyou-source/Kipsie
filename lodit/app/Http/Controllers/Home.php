<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\mo1;
use App\Models\Reservation;
use App\Models\Transaction;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Page;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\Category;
use App\Services\MathCaptcha;

use App\Helpers\NotificationHelper;

class Home extends Controller 
{
    
public function dok(Request $request)
{
    
     if (!$request->session()->has('id')) {
        return redirect('/login')->with('error', 'Please log in first.');
    }else{

    $hei = new mo1(); 

    $y = $hei->tampil('dok'); 
    $notbl = $hei->tampil('notsuspicious');
    $notatall = $hei->tampil('notatall');

    $joined = collect($y)->map(function ($dok) use ($notbl, $notatall) {
        $not = collect($notbl)->firstWhere('id', $dok->id);
        $not2 = collect($notatall)->firstWhere('id', $dok->id);

        return (object)[
            'id' => $dok->id,
            'nama' => $dok->nama,
            'foto' => $dok->foto ?? '',
            'MC' => $not->MC ?? 'None',
            'ML' => $not->ML ?? 'None',
            'personal_rating' => $not->personal_rating ?? 'None',
            'Website' => $not2->Website ?? 'None',
            'Safeness' => $not2->Safeness ?? 'None',
        ];
    });

    $hello = [
        'joined' => $joined,
            'notbl' => $notbl, 
            'y' => $y,
    ];

    
    

    return view('dok', $hello);
}
}

public function users(Request $request)
{
    if (!$request->session()->has('id')) {
        return redirect('/login')->with('error', 'Please log in first.');
    }

    $hei = new mo1();

    // Fetch your real tables
    $employees = $hei->tampil('employee');
    $users = $hei->tampil('login');
    $levels = $hei->tampil('level');

    // Join them together
    $joined = collect($employees)->map(function ($emp) use ($users, $levels) {
        $user = collect($users)->firstWhere('id', $emp->userid);
        $level = collect($levels)->firstWhere('lvlnumber', $user->level ?? null);

        return (object)[
        'employeeid' => $emp->employeeid,
        'userid' => $emp->userid,
        'employeename' => $emp->employeename,
        'employeeage' => $emp->employeeage,
        'employeegender' => $emp->employeegender,
        'employeebirthdate' => $emp->employeebirthdate,
        'employeerace' => $emp->employeerace,
        'employeereligion' => $emp->employeereligion,
        'employeebloodtype' => $emp->employeebloodtype,
        'employeeposition' => $emp->employeeposition,
        'username' => $user->username ?? 'No linked user',
        'level' => $user->level ?? 'N/A',
        'beingas' => $level->beingas ?? 'Unknown',

        ];
    });

    return view('users', ['joined' => $joined]);
}


    



public function logout(Request $request)
{
    $request->session()->flush();

    return redirect('/login')->with('success', 'Logged out successfully.');
}



    public function login()
    {
        // Use offline math captcha
        $isOnline = false;
        $captcha = MathCaptcha::generate();
        
        return view('login', [
            'is_online' => $isOnline,
            'captcha_question' => $captcha['question'],
            'captcha_answer' => $captcha['answer'],
            'recaptcha_key' => ''
        ]);
    }

public function aksi_login(Request $request)
{
    $username = $request->input('u');
    $password = $request->input('p');
    // Use offline math captcha
    $isOnline = false;

    // Validate captcha - check online or offline
    if ($isOnline) {
        // Verify Google reCAPTCHA
        $recaptchaToken = $request->input('g-recaptcha-response');
        $recaptchaSecret = config('services.recaptcha.secret_key', env('RECAPTCHA_SECRET_KEY'));

        if (!$this->verifyRecaptcha($recaptchaToken, $recaptchaSecret)) {
            return redirect('/login')->with('error', 'Captcha verification failed. Try again.');
        }
    } else {
        // Verify offline math captcha
        $userCaptchaAnswer = $request->input('captcha_answer');
        $correctCaptchaAnswer = $request->input('captcha_answer_hidden');

        if (!MathCaptcha::validate($userCaptchaAnswer, $correctCaptchaAnswer)) {
            return redirect('/login')->with('error', 'Wrong answer to math captcha. Try again.');
        }
    }

    // Safely query login table
    $user = null;
    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('login')) {
            $user = DB::table('login')->where('username', $username)->first();
        }
    } catch (\Exception $e) {
        // Table doesn't exist or error occurred
        return redirect('/login')->with('error', 'Database connection error. Please try again later.');
    }

    if ($user && Hash::check($password, $user->password)) {

        if ($user->is_active == 0) {
            return redirect('/login')
                ->with('error', 'Please activate your account via email.');
        }

        $request->session()->put('id', $user->id);
        $request->session()->put('username', $user->username);
        $request->session()->put('level', $user->level ?? 0);

        return redirect('/kli');

    } else {
        return redirect('/login')->with('error', 'Wrong username or password');
    }
}

/**
 * Verify Google reCAPTCHA response (v2 Checkbox)
 */
private function verifyRecaptcha($token, $secret)
{
    if (!$token || !$secret) {
        return false;
    }

    try {
        $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query([
                    'secret' => $secret,
                    'response' => $token
                ])
            ]
        ]));

        $result = json_decode($response, true);

        // v2 Checkbox returns success: true/false
        return isset($result['success']) && $result['success'] === true;
    } catch (\Exception $e) {
        // If verification fails, fall back to math captcha
        return false;
    }
}
                                                     


public function edit($id)

{
     
    $hei = new mo1();

    $dok = $hei->ya('dok', ['id' => $id]);
    if (is_array($dok)) {
        $dok = $dok[0];
    }

    $notbl = $hei->ya('notsuspicious', ['id' => $id]);
    $notatall = $hei->ya('notatall', ['id' => $id]);

    if (is_array($notbl)) {
        $notbl = $notbl[0];
    }
    if (is_array($notatall)) {
        $notatall = $notatall[0];
    }

    $joined = (object) [
        'id' => $dok->id ?? null,
        'nama' => $dok->nama ?? '',
        'foto' => $dok->foto ?? '',
        'MC' => $notbl->MC ?? '',
        'ML' => $notbl->ML ?? '',
        'Website' => $notatall->Website ?? '',
        'personal_rating' => $notbl->personal_rating ?? '',
        'Safeness' => $notatall->Safeness ?? '',
    ];
    return view('edit', ['dok' => $joined]);
}

public function EmployEdit($employeeid)
{
    $hei = new mo1();

    // Fetch employee info
    $emp = $hei->ya('employee', ['employeeid' => $employeeid]);
    if (is_array($emp)) {
        $emp = $emp[0];
    }

    // Fetch linked user and level (if any)
    $user = null;
    $level = null;
    if ($emp && isset($emp->userid)) {
        $user = $hei->ya('login', ['id' => $emp->userid]);
        if (is_array($user)) {
            $user = $user[0];
        }

        if ($user && isset($user->level)) {
            $level = $hei->ya('level', ['lvlnumber' => $user->level]);
            if (is_array($level)) {
                $level = $level[0];
            }
        }
    }

    // Merge all into one object
    $joined = (object)[
        'employeeid' => $emp->employeeid ?? null,
        'employeename' => $emp->employeename ?? '',
        'employeeage' => $emp->employeeage ?? '',
        'employeegender' => $emp->employeegender ?? '',
        'employeebirthdate' => $emp->employeebirthdate ?? '',
        'employeerace' => $emp->employeerace ?? '',
        'employeereligion' => $emp->employeereligion ?? '',
        'employeebloodtype' => $emp->employeebloodtype ?? '',
        'employeeposition' => $emp->employeeposition ?? '',
        'username' => $user->username ?? 'None',
        'level' => $user->level ?? '',
        'beingas' => $level->beinga ?? '',
    ];

    return view('EmployEdit', ['emp' => $joined]);
}

public function EmployUpdate(Request $request, $employeeid)
{
    if (!$request->session()->has('id')) {
        return redirect('/login')->with('error', 'Please log in first.');
    }

    $hei = new mo1();
    $where = ['employeeid' => $employeeid];

    // Update employee table
    $dataEmp = [
        'employeename' => $request->input('employeename'),
        'employeeage' => $request->input('employeeage'),
        'employeegender' => $request->input('employeegender'),
        'employeebirthdate' => $request->input('employeebirthdate'),
        'employeerace' => $request->input('employeerace'),
        'employeereligion' => $request->input('employeereligion'),
        'employeebloodtype' => $request->input('employeebloodtype'),
        'employeeposition' => $request->input('employeeposition'),
    ];

    $hei->edit('employee', $dataEmp, $where);

    // If user info also edited
    $userid = $request->input('userid');
    if ($userid) {
        $dataUser = [
            'username' => $request->input('username'),
            'level' => $request->input('level'),
        ];
        $hei->edit('login', $dataUser, ['id' => $userid]);
    }

    return redirect('/users')->with('success', 'Employee updated successfully!');
}

public function update(Request $request, $id)
{
    if (!$request->session()->has('id')) {
        return redirect('/login')->with('error', 'Please log in first.');
    }
    $hei = new mo1();
    $where = ['id' => $id];

    $dataDok = [
        'nama' => $request->input('nama'),
    ];

    if ($request->hasFile('foto')) {
        $path = $request->file('foto')->store('uploads', 'public');
        $dataDok['foto'] = $path;
    }

    $hei->edit('dok', $dataDok, $where);

    $dataNot = [
        'MC' => $request->input('MC'),
        'ML' => $request->input('ML'),
        'personal_rating' => $request->input('personal_rating'),
    ];
    $hei->edit('notsuspicious', $dataNot, $where);

    $dataNotAtAll = [
        'Website' => $request->input('Website'),
        'Safeness' => $request->input('Safeness'),
    ];
    $hei->edit('notatall', $dataNotAtAll, $where);

    return redirect('/dok');
}


public function wow()
{
    return view('wow');
}


public function hapus($id)
{
    
    
    $hei = new mo1();
    $where = ['id' => $id];

    $hei->hapus('notsuspicious', $where);
    $hei->hapus('notatall', $where);
    $hei->hapus('dok', $where);

    return redirect('/dok');
}

public function delEmployee($employeeid)
{
    $hei = new mo1();

    $employee = DB::table('employee')->where('employeeid', $employeeid)->first();

    if ($employee) {
        $hei->hapus('employee', ['employeeid' => $employeeid]);

        if (!empty($employee->userid)) {
            $hei->hapus('login', ['id' => $employee->userid]);
        }

        return redirect('/users')->with('success', 'Employee and linked user deleted successfully!');
    }

    return redirect('/users')->with('error', 'Employee not found!');
}


    public function tambah(Request $request)
    {
        if (!$request->session()->has('id')) {
        return redirect('/login')->with('error', 'Please log in first.'); }
        
        $hei = new mo1();
        echo view('tmbh');
        echo view('header');
        echo view('footer');
        echo view('menu');
    }

public function addUser(Request $request)
{
    if (!$request->session()->has('id')) {
        return redirect('/login')->with('error', 'Please log in first.');
    }

    // Only Admin (level 3) can add users
    if (session('level') != 3) {
        return redirect('/')->with('error', 'Access denied.');
    }

    echo view('header');
    echo view('addUser');
    echo view('footer');
    echo view('menu');
}

public function addEmployee(Request $request)
{
    if (!$request->session()->has('id')) {
        return redirect('/login')->with('error', 'Please log in first.');
    }

    echo view('header');
    echo view('employeeData');
    echo view('footer');
    echo view('menu');
}

public function saveEmployee(Request $request)
{
    if (!$request->session()->has('id')) {
        return redirect('/login')->with('error', 'Please log in first.');
    }

    $hei = new mo1();

    // 1️⃣ Create new user account in `login`
    $userData = [
        'username' => $request->input('username'),
        'password' => bcrypt($request->input('password')), // encrypt password!
        'level' => $request->input('level'), // e.g. 1 = Patient, 2 = Doctor, 3 = Admin
        'created_at' => now(),
        'updated_at' => now(),
    ];

    $newUserId = DB::table('login')->insertGetId($userData);

    // 2️⃣ Create employee entry linked to the new user
    $employeeData = [
        'userid' => $newUserId,
        'employeename' => $request->input('employeename'),
        'employeeage' => $request->input('employeeage'),
        'employeegender' => $request->input('employeegender'),
        'employeebirthdate' => $request->input('employeebirthdate'),
        'employeerace' => $request->input('employeerace'),
        'employeereligion' => $request->input('employeereligion'),
        'employeebloodtype' => $request->input('employeebloodtype'),
        'employeeposition' => $request->input('employeeposition'),
    ];

    $hei->insert('employee', $employeeData);

    return redirect('/users')->with('success', 'Employee and user account created successfully!');
}

public function resetPassword($userid)
{
    if (session('level') != 3) {
        return redirect('/users')->with('error', 'Unauthorized access.');
    }

    $defaultPassword =('password123'); 

    DB::table('login')
        ->where('id', $userid)
        ->update([
    'password' => bcrypt('password123'),
    'updated_at' => now()
]);


    return redirect('/users');
}

public function settings(Request $request)
{
    if (!$request->session()->has('id')) {
        return redirect('/login')->with('error', 'Please log in first.');
    }

    $userId = $request->session()->get('id');
    $userLevel = $request->session()->get('level');
    $hei = new mo1();

    // Get user info
    $user = DB::table('login')->where('id', $userId)->first();
    $employee = DB::table('employee')->where('userid', $userId)->first();
    $level = DB::table('level')->where('lvlnumber', $user->level)->first();

    $info = (object)[
        'userid' => $userId,
        'username' => $user->username,
        'password' => $user->password,
        'level' => $user->level,
        'beingas' => $level->beingas ?? 'Unknown',
        'employeename' => $employee->employeename ?? '',
        'employeeage' => $employee->employeeage ?? '',
        'employeegender' => $employee->employeegender ?? '',
        'employeebirthdate' => $employee->employeebirthdate ?? '',
        'employeerace' => $employee->employeerace ?? '',
        'employeereligion' => $employee->employeereligion ?? '',
        'employeebloodtype' => $employee->employeebloodtype ?? '',
        'employeeposition' => $employee->employeeposition ?? '',
    ];

    // Get app settings (for admin/superadmin only)
    $appSettings = null;
    if (in_array($userLevel, [3, 4, 5, 6])) {
        $appSettings = (object)[
            'app_name' => DB::table('app_settings')->where('setting_key', 'app_name')->value('setting_value') ?? 'KIPS',
            'app_logo_path' => DB::table('app_settings')->where('setting_key', 'app_logo_path')->value('setting_value') ?? 'foto/logo.jpg',
        ];
    }

    echo view('header');
    echo view('menu');
    echo view('settings', ['info' => $info, 'appSettings' => $appSettings, 'userLevel' => $userLevel]);
    echo view('footer');
}

public function updateSettings(Request $request)
{
    if (!$request->session()->has('id')) {
        return redirect('/login')->with('error', 'Please log in first.');
    }

    $userId = $request->session()->get('id');
    $userLevel = $request->session()->get('level');
    $hei = new mo1();

    $user = DB::table('login')->where('id', $userId)->first();
    $employee = DB::table('employee')->where('userid', $userId)->first();

    $userData = [
        'username' => $request->username ?: $user->username,
    ];

    if ($request->filled('password')) {
        $userData['password'] = bcrypt($request->password);
    }

    $hei->edit('login', $userData, ['id' => $userId]);

    $employeeData = [
        'employeename' => $request->employeename ?: $employee->employeename,
        'employeeage' => $request->employeeage ?: $employee->employeeage,
        'employeegender' => $request->employeegender ?: $employee->employeegender,
        'employeebirthdate' => $request->employeebirthdate ?: $employee->employeebirthdate,
        'employeerace' => $request->employeerace ?: $employee->employeerace,
        'employeereligion' => $request->employeereligion ?: $employee->employeereligion,
        'employeebloodtype' => $request->employeebloodtype ?: $employee->employeebloodtype,
        'employeeposition' => $request->employeeposition ?: $employee->employeeposition,
    ];

    $hei->edit('employee', $employeeData, ['userid' => $userId]);

    // Handle app settings for admin/superadmin
    if (in_array($userLevel, [3, 4, 5, 6])) {
        if ($request->filled('app_name')) {
            DB::table('app_settings')
                ->where('setting_key', 'app_name')
                ->update(['setting_value' => $request->app_name, 'updated_at' => now()]);
        }
        
        // Handle logo upload
        if ($request->hasFile('app_logo')) {
            $logoFile = $request->file('app_logo');
            $logoPath = $logoFile->move(public_path('foto'), $logoFile->getClientOriginalName());
            DB::table('app_settings')
                ->where('setting_key', 'app_logo_path')
                ->update(['setting_value' => 'foto/' . $logoFile->getClientOriginalName(), 'updated_at' => now()]);
        }
    }

    return redirect('/settings')->with('success', 'Settings updated successfully!');
}
   
public function simpan(Request $request)
{
    if (!$request->session()->has('id')) {
        return redirect('/login')->with('error', 'Please log in first.');
    }
    $hei = new mo1();

    if ($request->hasFile('foto')) {
        $path = $request->file('foto')->store('uploads', 'public');
    } else {
        $path = '';
    }

    $dataDok = [
        'nama' => $request->input('nama'),
        'foto' => $path
    ];
    $hei->tambah('dok', $dataDok);

    $lastid = DB::getPdo()->lastInsertid();

    $dataNot = [
        'id' => $lastid,
        'Title' => $request->input('Title'), 
        'MC' => $request->input('MC'),
        'ML' => $request->input('ML'),
        'personal_rating' => $request->input('personal_rating'),
    ];
    $hei->tambah('notsuspicious', $dataNot);

    $dataNotAtAll = [
        'id' => $lastid,
        'Title' => $request->input('Title'),
        'Website' => $request->input('Website'),
        'Safeness' => $request->input('Safeness'),
    ];
    $hei->tambah('notatall', $dataNotAtAll);

    return redirect('/dok');
}


public function register()
{
        // Use offline math captcha
        $isOnline = false;
        $captcha = MathCaptcha::generate();
    echo view('loginheader');
    echo view('register', [
        'is_online' => $isOnline,
        'captcha_question' => $captcha['question'],
        'captcha_answer' => $captcha['answer'],
        'recaptcha_key' => ''
    ]);
}

 
public function register_save(Request $request)
{
    // Use offline math captcha
    $isOnline = false;

    // Validate captcha - check online or offline
    if ($isOnline) {
        // Verify Google reCAPTCHA
        $recaptchaToken = $request->input('g-recaptcha-response');
        $recaptchaSecret = config('services.recaptcha.secret_key', env('RECAPTCHA_SECRET_KEY'));

        if (!$this->verifyRecaptcha($recaptchaToken, $recaptchaSecret)) {
            return redirect('/register')->with('error', 'Captcha verification failed. Try again.');
        }
    } else {
        // Verify offline math captcha
        $userCaptchaAnswer = $request->input('captcha_answer');
        $correctCaptchaAnswer = $request->input('captcha_answer_hidden');

        if (!MathCaptcha::validate($userCaptchaAnswer, $correctCaptchaAnswer)) {
            return redirect('/register')->with('error', 'Wrong answer to math captcha. Try again.');
        }
    }

    $request->validate([
        'u' => 'required|min:3',
        'email' => 'required|email|unique:login,email',
        'p' => 'required|min:6',
        'age' => 'required|integer|min:13|max:120',
    ]);

    $token = Str::random(64);

    DB::table('login')->insert([
        'username' => $request->u,
        'email' => $request->email,
        'password' => bcrypt($request->p),
        'age' => $request->age,
        'activation_token' => $token,
        'is_active' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Mail::send('emails.activate', [
        'token' => $token,
        'email' => $request->email,
    ], function ($message) use ($request) {
        $message->to($request->email);
        $message->subject('Activate Your Account');
    });

    return redirect('/login')->with(
        'success',
        'Account created. Please check your email to activate.'
    );
}


public function activate($token)
{
    $user = DB::table('login')
        ->where('activation_token', $token)
        ->first();

    if (!$user) {
        return redirect('/login')->with('error', 'Invalid activation link.');
    }

    DB::table('login')
        ->where('id', $user->id)
        ->update([
            'is_active' => 1,
            'activation_token' => null,
        ]);

    return redirect('/login')->with('success', 'Account activated! You can log in.');
}



public function kli(Request $request)
{
    if (!$request->session()->has('id')) {
        return redirect('/login')->with('error', 'Please log in first.');
    }
    $hei = new mo1(); 

    $y = $hei->tampil('dok'); 
    $regis = $hei->tampil('login');
    $notbl = $hei->tampil('notsuspicious');
    $notatall = $hei->tampil('notatall');

    $joined = collect($y)->map(function ($dok) use ($notbl, $notatall) {
       $not = collect($notbl)->firstWhere('id', $dok->id);
       $not2 = collect($notatall)->firstWhere('id', $dok->id);

    return [
            'dok' => $dok,
            'not' => $not,
            'not2' => $not2
    	   ];
    });

    $hello = [
        'joined' => $joined,
        'notbl' => $notbl, 
        'y' => $y,
             ];

    return view('kli', compact('y'));
}

public function report()
{
    $hei = new mo1();
    return view('report');
}

public function index() {
    $medicines = Medicine::all();
    return response()->json($medicines);
}

public function medicinesList() {
    $medicines = Medicine::select('id', 'name')->get();
    return response()->json($medicines);
}

public function getTransactions() {
    $transactions = Transaction::orderBy('date', 'desc')->get();
    return response()->json($transactions);
}

public function storeyao(Request $request)
{
    // Check permission - only level 3 and 4 can add medicines
    if (!$request->session()->has('id')) {
        return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
    }
    
    $userLevel = $request->session()->get('level');
    if ($userLevel !== 3 && $userLevel !== 4) {
        return response()->json(['success' => false, 'message' => 'You do not have permission to add medicines'], 403);
    }
    
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric',
        'stock' => 'required|integer',
        'category_id' => 'nullable|exists:categories,id',
        'age_restriction' => 'nullable|string|max:50',
        'expiry_date' => 'nullable|date',
        'images' => 'nullable|array',
        'images.*' => 'image|max:2048'
    ]);

    $medicine = new Medicine();
    $medicine->name = $validated['name'];
    $medicine->description = $validated['description'] ?? '';
    $medicine->price = $validated['price'];
    $medicine->stock = $validated['stock'];
    if (!empty($validated['category_id'])) {
        $medicine->category_id = $validated['category_id'];
    }
    if (!empty($validated['age_restriction'])) {
        $medicine->age_restriction = $validated['age_restriction'];
    }
    if (!empty($validated['expiry_date'])) {
        $medicine->expiry_date = $validated['expiry_date'];
    }

    $imagePaths = [];
    if ($request->hasFile('images')) {
        try {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('medicines', $filename, 'public');
                $imagePaths[] = $path;
            }
            $medicine->images = $imagePaths;
            // Set first image as primary
            if (!empty($imagePaths)) {
                $medicine->image = $imagePaths[0];
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to upload images: ' . $e->getMessage()], 400);
        }
    }

    $medicine->save();

    return response()->json(['success' => true, 'medicine' => $medicine]);
}


public function edityao(Request $request, $id) {
    $medicine = Medicine::findOrFail($id);
    $medicine->update($request->all());
    return response()->json($medicine);
}

public function delete($id) {
    $medicine = Medicine::findOrFail($id);
    $medicine->delete();
    return response()->json(['message' => 'Deleted successfully']);
}


 public function indext(Request $request)
 
    {
        if (!$request->session()->has('id')) {
        return redirect('/login')->with('error', 'Please log in first.');
    }
        $transactions = Transaction::orderBy('date', 'desc')->get();

        $totalIncome = Transaction::where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        echo view('header');
        echo view('menu');
        echo view('index', compact('transactions', 'totalIncome', 'totalExpense', 'balance'));
        echo view('footer');
    }

    public function storet(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        Transaction::create($request->all());

        return redirect()->back()->with('success', 'Transaction added successfully!');
    }

    public function exportPdf()
{
    $transactions = Transaction::all();

    $totalIncome = $transactions->where('type', 'income')->sum('amount');
    $totalExpense = $transactions->where('type', 'expense')->sum('amount');
    $balance = $totalIncome - $totalExpense;

    $pdf = Pdf::loadView('transactions.pdf', compact('transactions', 'totalIncome', 'totalExpense', 'balance'));

    return $pdf->download('financial_report.pdf');
}

    public function exportExcel()
    {
        return Excel::download(new TransactionsExport, 'financial_report.xlsx');
    }

    public function export()
{
    return Excel::download(new TransactionsExport, 'financial_report.xlsx');
}
    

    public function clearTransactions()
{
    \App\Models\Transaction::truncate(); // Deletes all rows

    return redirect()->route('transactions.index')->with('success', 'All transactions have been cleared.');
}

    public function destroyTransaction($id)
{
    $transaction = \App\Models\Transaction::findOrFail($id);
    $transaction->delete();

    return redirect()->route('transactions.index')->with('success', 'Transaction deleted.');
}

public function filterTransactions(Request $request)
{
    if(!$request->session()->has('id')) {
        return redirect('/login')->with('error','Please login first.');

    }

    $query = Transaction::query();

    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

     if ($request->filled('category')) {
        $query->where('category', 'like', '%'. $request->type. '%');
     }

      if ($request->filled('from_date') && $request->filled('until_date')) {
        $query->whereBetween('date', [$request->from_date, $request->until_date]);
      }

      $transactions = $query->orderBy('date', 'desc')->get();

      $totalIncome = $transactions->where('type','income')->sum('amount');
      $totalExpense = $transactions->where('type','expense')->sum('amount');
      $balance = $totalIncome - $totalExpense;

      echo view('header');
      echo view('menu');
      echo view('index', compact('transactions','totalIncome','totalExpense','balance'));
      echo view('footer');
}

public function medicineReport(Request $request)
{
    if (!$request->session()->has('id')) {
        return redirect('/login')->with('error', 'Please log in first.');
    }

    $userLevel = $request->session()->get('level');
    // Only allow staff levels 3+ to access reports
    if ($userLevel < 3) {
        return redirect('/yao')->with('error', 'Unauthorized access');
    }

    // Get all transactions ordered by oldest first to calculate running balance
    $allTransactions = DB::table('transactions')
        ->orderBy('created_at', 'asc')
        ->get();

    // Calculate running balance for each transaction
    $runningBalance = 0;
    $transactions = $allTransactions->map(function($transaction) use (&$runningBalance) {
        $runningBalance += $transaction->amount;
        $transaction->balance = $runningBalance;
        return $transaction;
    });

    // Reverse for descending display
    $transactions = $transactions->reverse();

    // Calculate summary statistics
    $totalIncome = DB::table('transactions')
        ->where('type', 'sale')
        ->sum('amount');

    $totalExpenses = DB::table('transactions')
        ->where('type', 'stock_addition')
        ->sum('amount'); // Will be negative

    $netBalance = ($totalIncome ?? 0) + ($totalExpenses ?? 0);

    // Get transaction breakdown by type
    $salesCount = DB::table('transactions')
        ->where('type', 'sale')
        ->count();

    $stockAdditions = DB::table('transactions')
        ->where('type', 'stock_addition')
        ->count();

    echo view('header');
    echo view('menu');
    echo view('medicine-report', [
        'transactions' => $transactions,
        'totalIncome' => $totalIncome ?? 0,
        'totalExpenses' => abs($totalExpenses ?? 0),
        'netBalance' => $netBalance,
        'salesCount' => $salesCount,
        'stockAdditions' => $stockAdditions
    ]);
    echo view('footer');
}

public function deleteTransaction(Request $request, $transactionId)
{
    if (!$request->session()->has('id')) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    $userLevel = $request->session()->get('level');
    // Only allow staff levels 3+ to delete
    if ($userLevel < 3) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    $transaction = DB::table('transactions')->where('id', $transactionId)->first();
    
    if (!$transaction) {
        return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
    }

    // Record the deletion
    DB::table('transaction_deletions')->insert([
        'transaction_id' => $transactionId,
        'deleted_by_user_id' => $request->session()->get('id'),
        'action' => 'soft_delete',
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'transaction_data' => json_encode($transaction),
        'deleted_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Soft delete the transaction
    DB::table('transactions')
        ->where('id', $transactionId)
        ->update(['deleted_at' => now()]);

    return response()->json(['success' => true, 'message' => 'Transaction deleted']);
}

public function deletedTransactions(Request $request)
{
    if (!$request->session()->has('id')) {
        return redirect('/login')->with('error', 'Please log in first.');
    }

    $userLevel = $request->session()->get('level');
    // Only allow super admin (level 4) to access deleted transactions
    if ($userLevel != 4) {
        return redirect('/medicine-report')->with('error', 'Only super admins can access deleted transactions');
    }

    // Get all deleted transactions with deletion info
    $deletions = DB::table('transaction_deletions')
        ->leftJoin('login', 'transaction_deletions.deleted_by_user_id', '=', 'login.id')
        ->select(
            'transaction_deletions.*',
            'login.username as deleted_by_username'
        )
        ->orderBy('transaction_deletions.deleted_at', 'desc')
        ->get();

    echo view('header');
    echo view('menu');
    echo view('deleted-transactions', ['deletions' => $deletions]);
    echo view('footer');
}

public function restoreTransaction(Request $request, $deletionId)
{
    if (!$request->session()->has('id')) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    $userLevel = $request->session()->get('level');
    // Only super admin can restore
    if ($userLevel != 4) {
        return response()->json(['success' => false, 'message' => 'Only super admins can restore transactions'], 403);
    }

    $deletion = DB::table('transaction_deletions')->where('id', $deletionId)->first();
    
    if (!$deletion) {
        return response()->json(['success' => false, 'message' => 'Deletion record not found'], 404);
    }

    // Restore the transaction
    DB::table('transactions')
        ->where('id', $deletion->transaction_id)
        ->update(['deleted_at' => null]);

    // Update deletion record to show it was restored
    DB::table('transaction_deletions')
        ->where('id', $deletionId)
        ->update([
            'action' => 'restored',
            'updated_at' => now()
        ]);

    return response()->json(['success' => true, 'message' => 'Transaction restored']);
}

public function permanentlyDeleteTransaction(Request $request, $deletionId)
{
    if (!$request->session()->has('id')) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    $userLevel = $request->session()->get('level');
    // Only super admin can permanently delete
    if ($userLevel != 4) {
        return response()->json(['success' => false, 'message' => 'Only super admins can permanently delete'], 403);
    }

    $deletion = DB::table('transaction_deletions')->where('id', $deletionId)->first();
    
    if (!$deletion) {
        return response()->json(['success' => false, 'message' => 'Deletion record not found'], 404);
    }

    // Permanently delete the transaction
    DB::table('transactions')->where('id', $deletion->transaction_id)->delete();

    // Update deletion record
    DB::table('transaction_deletions')
        ->where('id', $deletionId)
        ->update([
            'action' => 'permanent_delete',
            'updated_at' => now()
        ]);

    return response()->json(['success' => true, 'message' => 'Transaction permanently deleted']);
}

public function yao(Request $request)
 
    {
        if (!$request->session()->has('id')) {
        return redirect('/login')->with('error', 'Please log in first.');
    }
     
    $username = $request->session()->get('username', 'Guest');
   

    return view('yao', compact('username'));
    }

    public function storem(Request $request)
{
    $data = $request->validate([
        'customer_name' => 'required|string|max:255',
        'items' => 'required|array',
        'total' => 'required|numeric'
    ]);

    // Validate stock before creating order
    foreach ($data['items'] as $item) {
        $medicine = Medicine::findOrFail($item['id']);
        if ($medicine->stock < $item['quantity']) {
            return response()->json([
                'success' => false,
                'message' => "{$medicine->name} has insufficient stock. Available: {$medicine->stock}, Requested: {$item['quantity']}"
            ], 400);
        }
    }

    // Decrease stock for each item
    foreach ($data['items'] as $item) {
        Medicine::where('id', $item['id'])->decrement('stock', $item['quantity']);
    }

    $order = DB::table('orders')->insertGetId([
        'customer_name' => $data['customer_name'],
        'items' => json_encode($data['items']),
        'total' => $data['total'],
        'user_id' => session('id'),
        'status' => 'pending',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Create payment confirmation record for cashier to process
    DB::table('payment_confirmations')->insert([
        'order_id' => $order,
        'user_id' => session('id'),
        'amount' => $data['total'],
        'payment_method' => 'pending',
        'status' => 'pending',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Create transaction record for income
    $currentBalance = DB::table('transactions')->sum('amount') ?? 0;
    DB::table('transactions')->insert([
        'type' => 'income',
        'category' => 'orders',
        'description' => "Order #{$order} - Customer: {$data['customer_name']}",
        'amount' => $data['total'],
        'balance' => $currentBalance + $data['total'],
        'date' => now()->format('Y-m-d'),
        'reference_id' => $order,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Send notification to admins about new order
    NotificationHelper::notifyOrderPlaced($order, $data['customer_name'], $data['total'], count($data['items']));

    return response()->json([
        'success' => true,
        'order_id' => $order
    ]);
}

public function storeMedicine(Request $request) {
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric',
        'stock' => 'required|integer|min:0',
        'image' => 'nullable|image|max:2048'
    ]);

    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('medicines', 'public');
    }

    $id = DB::table('medicines')->insertGetId([
        'name' => $data['name'],
        'description' => $data['description'] ?? '',
        'price' => $data['price'],
        'stock' => $data['stock'],
        'image' => $data['image'] ?? null,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    return response()->json(['success' => true, 'id' => $id]);
}

public function updateMedicine(Request $request, $id)
{
    // Check permission - only level 2 and 3 can edit medicines
    if (!$request->session()->has('id')) {
        return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
    }
    
    $userLevel = $request->session()->get('level');
    if ($userLevel !== 3 && $userLevel !== 4) {
        return response()->json(['success' => false, 'message' => 'You do not have permission to edit medicines'], 403);
    }
    
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric',
        'stock' => 'required|integer|min:0',
        'category_id' => 'nullable|exists:categories,id',
        'age_restriction' => 'nullable|string|max:50',
        'expiry_date' => 'nullable|date',
        'images' => 'nullable|array',
        'images.*' => 'image|max:2048'
    ]);

    $medicine = Medicine::findOrFail($id);

    $medicine->name = $request->input('name');
    $medicine->description = $request->input('description');
    $medicine->price = $request->input('price');
    $medicine->stock = $request->input('stock');
    
    if ($request->has('category_id') && !empty($request->input('category_id'))) {
        $medicine->category_id = $request->input('category_id');
    }
    
    if ($request->has('age_restriction') && !empty($request->input('age_restriction'))) {
        $medicine->age_restriction = $request->input('age_restriction');
    }
    
    if ($request->has('expiry_date') && !empty($request->input('expiry_date'))) {
        $medicine->expiry_date = $request->input('expiry_date');
    }

    if ($request->hasFile('images')) {
        try {
            // Delete old images if replacing
            if ($medicine->images && is_array($medicine->images)) {
                foreach ($medicine->images as $oldImage) {
                    if (file_exists(storage_path('app/public/' . $oldImage))) {
                        unlink(storage_path('app/public/' . $oldImage));
                    }
                }
            } elseif ($medicine->image) {
                if (file_exists(storage_path('app/public/' . $medicine->image))) {
                    unlink(storage_path('app/public/' . $medicine->image));
                }
            }
            
            // Upload new images
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('medicines', $filename, 'public');
                $imagePaths[] = $path;
            }
            
            $medicine->images = $imagePaths;
            // Set first image as primary
            if (!empty($imagePaths)) {
                $medicine->image = $imagePaths[0];
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to upload images: ' . $e->getMessage()], 400);
        }
    }

    $medicine->save();

    return response()->json(['success' => true, 'medicine' => $medicine]);
}

public function deleteMedicine($id) {
    // Soft-delete medicines by setting `hidden` flag and log the action
    $old = DB::table('medicines')->where('id', $id)->first();
    if (!$old) {
        return response()->json(['success' => false, 'message' => 'Medicine not found'], 404);
    }

    // Mark as hidden
    DB::table('medicines')->where('id', $id)->update([
        'hidden' => 1,
        'updated_at' => now(),
    ]);

    // Try to log to pending_admin_changes (best-effort)
    try {
        DB::table('pending_admin_changes')->insert([
            'action_type' => 'DELETE',
            'target_user_id' => $id,
            'admin_id' => session('id') ?? null,
            'old_data' => json_encode((array)$old),
            'new_data' => null,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        try {
            \App\Services\DiscordNotifier::notify("Medicine deleted: id={$id} by admin=" . (session('id') ?? 'unknown'));
        } catch (\Exception $e) {}
    } catch (\Exception $e) {
        // ignore logging errors
    }

    return response()->json(['success' => true]);
}

public function medtransactions(Request $request)
{
if (!$request->session()->has('id')) {
        return redirect('/login')->with('error', 'Please log in first.');
    }

    $query = DB::table('orders');

    // ✅ date filter
    if ($request->filled('from_date') && $request->filled('until_date')) {
        $query->whereBetween('created_at', [
            $request->from_date . ' 00:00:00',
            $request->until_date . ' 23:59:59'
        ]);
    }

    // ✅ sort order
    $sort = $request->get('sort', 'desc');
    $orders = $query->orderBy('created_at', $sort)->get();

    $totalSales = $orders->sum('total');



    // Defensive: ensure $orders is iterable (some bad codepaths may pass a string)
    if (is_string($orders)) {
        $decoded = json_decode($orders, true);
        if (is_array($decoded)) {
            $orders = collect($decoded);
        } else {
            $orders = collect();
        }
    } elseif (!($orders instanceof \Illuminate\Support\Collection)) {
        $orders = collect($orders);
    }

        // Defensive: ensure $orders is iterable (some bad codepaths may pass a string)
    if (is_string($orders)) {
        $decoded = json_decode($orders, true);
        if (is_array($decoded)) {
            $orders = collect($decoded);
        } else {
            $orders = collect();
        }
    } elseif (!($orders instanceof \Illuminate\Support\Collection)) {
        $orders = collect($orders);
    }

    // Decode `items` for each order defensively (handle double-encoded JSON or plain strings)
    $orders = $orders->map(function ($order) {
        $raw = $order->items ?? '';

        $decoded = null;
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            // If first decode returns a string, it was double-encoded: decode again
            if (is_string($decoded)) {
                $decoded2 = json_decode($decoded, true);
                if (is_array($decoded2)) {
                    $decoded = $decoded2;
                } else {
                    $decoded = null;
                }
            }
        } elseif (is_array($raw)) {
            $decoded = $raw;
        }

        if (!is_array($decoded)) {
            $decoded = [];
        }

        $order->items = $decoded;
        return $order;
    });

    return view('medtransactions', compact('orders', 'totalSales'));
}


public function clearMedTransactions()
{
    DB::table('orders')->truncate();

    return redirect('/medtransactions')->with('success', 'All medicine transactions cleared!');
}

public function exportMedPdf(Request $request)
{
    $query = DB::table('orders');

    if ($request->filled('from_date') && $request->filled('until_date')) {
        $query->whereBetween('created_at', [
            $request->from_date . ' 00:00:00',
            $request->until_date . ' 23:59:59'
        ]);
    }

    $orders = $query->orderBy(
        'created_at',
        $request->get('sort', 'desc')
    )->get();

    $totalSales = $orders->sum('total');

    $pdf = Pdf::loadView(
        'medtransactions_pdf',
        compact('orders', 'totalSales')
    );

    return $pdf->download('medicine_transactions.pdf');
}


public function exportMedExcel(Request $request)
{
    $query = DB::table('orders');

    if ($request->filled('from_date') && $request->filled('until_date')) {
        $query->whereBetween('created_at', [
            $request->from_date . ' 00:00:00',
            $request->until_date . ' 23:59:59'
        ]);
    }

    $orders = $query->orderBy(
        'created_at',
        $request->get('sort', 'desc')
    )->get();

    $filename = 'medicine_transactions_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

    return Excel::download(
        new \App\Exports\GenericExport($orders),
        $filename
    );
}

 
public function list()
{
    return response()->json([
        'success' => true,
        'data' => Medicine::all()
    ]);
}



public function add(Request $request)
{
    $medicine = Medicine::create([
        'name' => $request->name,
        'price' => $request->price,
        'stock' => $request->stock
    ]);

    return response()->json([
        'success' => true,
        'product' => $medicine
    ]);
}

public function Mededit(Request $request)
{
    $medicine = Medicine::findOrFail($request->id);

    $medicine->update([
        'name' => $request->name,
        'price' => $request->price,
        'stock' => $request->stock
    ]);

    return response()->json(['success' => true]);
}




     public function IEAedit($userId)
{
    $hei = new mo1();

    $user = $hei->ya('login', ['id' => $userId]);
    if (is_array($user)) {
        $user = (object)$user[0];
    }

    $pages = $hei->tampil('pages'); 

    $userPages = $hei->ya('user_page', ['user_id' => $userId]); 
    $user->pages = collect($userPages)->pluck('page_id'); 

    return view('user_access.edit', compact('user', 'pages'));
}
public function indexmm()
    {
        return response()->json([
            'success' => true,
            'data' => Medicine::all()
        ]);
    }

    public function IEAupdate(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $accessData = [];
        foreach ($request->pages ?? [] as $pageId) {
            $accessData[$pageId] = ['can_access' => true];
        }

        // Sync access, remove unchecked
        $user->pages()->sync($accessData);

        return redirect()->back()->with('success', 'Access updated!');
    }

    public function iea(){
        echo view('page');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_process,delivered,cancelled',
        ]);

        $order = Order::findOrFail($id);

        $order->status = $request->status;
        $order->save();

        return back()->with('success', 'Order status updated');
    }

    public function getMedicines() {
        $medicines = Medicine::with('category')->get();
        return response()->json(['success' => true, 'data' => $medicines]);
    }

    public function getCategories() {
        $categories = Category::whereNull('parent_id')->with('subcategories')->get();
        return response()->json(['success' => true, 'data' => $categories]);
    }
    public function stockManagementPage(Request $request) {
        if (!$request->session()->has('id')) {
            return redirect('/login')->with('error', 'Please log in first.');
        }
        
        // Only allow admins, pharmacists, owners, super admins
        $userLevel = $request->session()->get('level');
        if (!in_array($userLevel, [1, 2, 3, 4, 5, 6])) {
            return redirect('/yao')->with('error', 'Access denied.');
        }

        $medicines = Medicine::all();
        
        return view('stock-management', compact('medicines'));
    }

    public function updateStock(Request $request) {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'required|integer|min:1',
            'action' => 'required|in:add,set'
        ]);

        $medicine = Medicine::findOrFail($request->medicine_id);
        $oldStock = $medicine->stock;
        $quantityAdded = 0;
        
        if ($request->action === 'add') {
            $quantityAdded = $request->quantity;
            $medicine->increment('stock', $request->quantity);
        } else {
            // 'set' action - replace stock value
            $quantityAdded = $request->quantity - $oldStock;
            $medicine->stock = $request->quantity;
            $medicine->save();
        }

        // Record transaction for stock addition (expense)
        if ($quantityAdded > 0) {
            $costPerUnit = $medicine->price; // Use medicine price as unit cost
            $totalCost = $costPerUnit * $quantityAdded;
            $currentBalance = DB::table('transactions')->sum('amount') ?? 0;
            
            DB::table('transactions')->insert([
                'type' => 'stock_addition',
                'description' => "Stock added for {$medicine->name} - {$quantityAdded} units @ \${$costPerUnit} each",
                'medicine_id' => $medicine->id,
                'quantity' => $quantityAdded,
                'amount' => -$totalCost, // Negative because it's an expense
                'balance' => $currentBalance - $totalCost,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully',
            'new_stock' => $medicine->stock
        ]);
    }

    // ================== RECEIPT GENERATION ==================
    public function generateReceipt(Request $request)
    {
        $orderId = $request->input('order_id');
        $order = Order::with('items.medicine')->find($orderId);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        $receiptHtml = view('receipts.receipt-template', ['order' => $order])->render();
        
        return response()->json([
            'success' => true,
            'html' => $receiptHtml,
            'order_id' => $order->id,
            'total' => $order->total,
            'date' => $order->created_at->format('Y-m-d H:i')
        ]);
    }

    public function downloadReceipt($orderId)
    {
        $order = Order::with('items.medicine')->find($orderId);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        $pdf = Pdf::loadView('receipts.receipt-template', ['order' => $order]);
        return $pdf->download('receipt_' . $order->id . '.pdf');
    }

    public function sendReceiptEmail(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'email' => 'required|email'
        ]);

        $orderId = $request->input('order_id');
        $email = $request->input('email');
        $order = Order::with('items.medicine')->find($orderId);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        try {
            $pdf = Pdf::loadView('receipts.receipt-template', ['order' => $order]);
            
            Mail::send('receipts.email-receipt', ['order' => $order], function ($message) use ($email, $pdf, $orderId) {
                $message->to($email)
                    ->subject("Receipt for Order #" . $orderId)
                    ->attachData($pdf->output(), "receipt_$orderId.pdf", [
                        'mime' => 'application/pdf',
                    ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Receipt sent to ' . $email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending email: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendReceiptSMS(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'phone' => 'required|string'
        ]);

        $orderId = $request->input('order_id');
        $phone = $request->input('phone');
        $order = Order::with('items.medicine')->find($orderId);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        // Format SMS message
        $message = "LODIT Receipt - Order #" . $order->id . "\n";
        $message .= "Total: $" . number_format($order->total, 2) . "\n";
        $message .= "Items: " . $order->items->count() . "\n";
        $message .= "Thank you for your purchase!";

        // TODO: Integrate with SMS provider (Twilio, etc.)
        // For now, just return success
        return response()->json([
            'success' => true,
            'message' => 'Receipt SMS sent to ' . $phone,
            'preview' => $message
        ]);
    }

    /**
     * Send receipt via email (from payment modal - accepts order data directly)
     */
    public function sendReceiptEmailFromModal(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'order' => 'required|array',
            'order.customer_name' => 'required|string',
            'order.items' => 'required|array',
            'order.total' => 'required|numeric',
            'order.date' => 'required|string'
        ]);

        $email = $validated['email'];
        $order = $validated['order'];

        try {
            // Build HTML email content
            $itemsHtml = '';
            foreach ($order['items'] as $item) {
                $itemsHtml .= "<tr>
                    <td>{$item['name']}</td>
                    <td>{$item['quantity']}</td>
                    <td>\${$item['price']}</td>
                    <td>\$" . number_format($item['price'] * $item['quantity'], 2) . "</td>
                </tr>";
            }

            $emailContent = "
            <h2>Order Receipt</h2>
            <p><strong>Customer:</strong> {$order['customer_name']}</p>
            <p><strong>Date:</strong> {$order['date']}</p>
            <h3>Order Details</h3>
            <table border='1' cellpadding='10'>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
                {$itemsHtml}
            </table>
            <h3>Total: \$" . number_format($order['total'], 2) . "</h3>
            <p>Thank you for your purchase!</p>";

            // Send email using Mail facade
            \Illuminate\Support\Facades\Mail::send([], [], function($message) use ($email, $emailContent, $order) {
                $message->to($email)
                    ->subject('LODIT Receipt - ' . $order['customer_name'])
                    ->html($emailContent);
            });

            return response()->json([
                'success' => true,
                'message' => 'Receipt sent to ' . $email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send receipt via WhatsApp (from payment modal - accepts order data directly)
     */
    public function sendReceiptWhatsAppFromModal(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'order' => 'required|array',
            'order.customer_name' => 'required|string',
            'order.items' => 'required|array',
            'order.total' => 'required|numeric',
            'order.date' => 'required|string'
        ]);

        $phone = $validated['phone'];
        $order = $validated['order'];

        try {
            // Format WhatsApp message
            $message = "📄 *LODIT ORDER RECEIPT* \n\n";
            $message .= "👤 Customer: {$order['customer_name']}\n";
            $message .= "📅 Date: {$order['date']}\n\n";
            $message .= "*Items:*\n";
            
            foreach ($order['items'] as $item) {
                $total = $item['price'] * $item['quantity'];
                $message .= "• {$item['name']} x{$item['quantity']} = \${$total}\n";
            }
            
            $message .= "\n💰 *Total: \$" . number_format($order['total'], 2) . "*\n\n";
            $message .= "Thank you for your purchase! ✨";

            // TODO: Integrate with WhatsApp API (Twilio, WhatsApp Business API, etc.)
            // For now, return success message with preview
            return response()->json([
                'success' => true,
                'message' => 'Receipt will be sent to ' . $phone . ' via WhatsApp',
                'preview' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error preparing WhatsApp message: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyAge(Request $request)
    {
        $request->validate([
            'birth_date' => 'required|date',
            'medicine_id' => 'required|integer'
        ]);

        $birthDate = \Carbon\Carbon::parse($request->input('birth_date'));
        $age = $birthDate->diffInYears(\Carbon\Carbon::now());
        $medicine = Medicine::find($request->input('medicine_id'));

        if (!$medicine) {
            return response()->json(['success' => false, 'message' => 'Medicine not found'], 404);
        }

        // Check if medicine has age restriction
        if (!$medicine->age_restriction) {
            return response()->json(['success' => true, 'verified' => true, 'message' => 'No age restriction']);
        }

        // Parse age restriction (e.g., "18+", "21+")
        preg_match('/(\d+)\+?/', $medicine->age_restriction, $matches);
        $requiredAge = isset($matches[1]) ? (int)$matches[1] : 0;

        $verified = $age >= $requiredAge;

        return response()->json([
            'success' => true,
            'verified' => $verified,
            'age' => $age,
            'required_age' => $requiredAge,
            'message' => $verified ? 'Age verified' : 'Age not sufficient for this medicine'
        ]);
    }

    // Password Reset Methods
    public function forgotPassword()
    {
        return view('forgot-password');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = trim($request->input('email'));

        // Check if email exists in login table
        $user = DB::table('login')->where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email not found in our system.']);
        }

        // Generate a random 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(15); // Code expires in 15 minutes

        // Store the reset code in cache or database
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'code' => $code,
                'expires_at' => $expiresAt,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // Send email with reset code
        try {
            Mail::send('emails.password-reset-code', ['code' => $code, 'name' => $user->username ?? 'User', 'email' => $email], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Your Password Reset Code');
            });

            return redirect('/verify-reset-code')->with(['email' => $email, 'success' => 'Check your email for the reset code. It expires in 15 minutes.']);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send reset code. Please try again.']);
        }
    }

    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6'
        ]);

        $email = $request->input('email');
        $code = $request->input('code');

        // Check if the reset code exists and is not expired
        $resetRecord = DB::table('password_resets')
            ->where('email', $email)
            ->where('code', $code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$resetRecord) {
            return back()->withErrors([
                'code' => 'Invalid or expired reset code. Please try again.'
            ])->with('email', $email);
        }

        // Code is valid, redirect to password reset form
        return view('reset-password', [
            'email' => $email,
            'code' => $code
        ]);
    }

    public function showVerifyCodeForm(Request $request)
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect('/forgot-password');
        }

        return view('verify-reset-code', [
            'email' => $email
        ]);
    }

    public function resendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = trim($request->input('email'));

        // Check if email exists in login table
        $user = DB::table('login')->where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email not found in our system.']);
        }

        // Generate new code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(15);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'code' => $code,
                'expires_at' => $expiresAt,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // Send email
        try {
            Mail::send('emails.password-reset-code', ['code' => $code, 'name' => $user->username ?? 'User', 'email' => $email], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Your New Password Reset Code');
            });

            return back()->with('success', 'A new code has been sent to your email.');
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send code. Please try again.']);
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
            'password' => 'required|min:8|confirmed'
        ]);

        $email = $request->input('email');
        $code = $request->input('code');
        $newPassword = $request->input('password');

        // Verify the code is still valid
        $resetRecord = DB::table('password_resets')
            ->where('email', $email)
            ->where('code', $code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$resetRecord) {
            return back()->withErrors([
                'code' => 'Invalid or expired reset code. Please request a new one.'
            ]);
        }

        // Find the user in login table
        $user = DB::table('login')->where('email', $email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'User not found.'
            ]);
        }

        // Update password in login table
        DB::table('login')
            ->where('id', $user->id)
            ->update([
                'password' => Hash::make($newPassword),
                'updated_at' => now()
            ]);

        // Delete the reset record
        DB::table('password_resets')->where('email', $email)->delete();

        return redirect('/login')->with('success', 'Password updated successfully! Please log in with your new password.');
    }

    // Patient medicine purchase history (customer view)
    public function medicineHistory(Request $request)
    {
        if (!$request->session()->has('id')) {
            return redirect('/login')->with('error', 'Please log in first.');
        }

        // Only patients (level 1) can access
        if ((int) ($request->session()->get('level') ?? 0) !== 1) {
            return redirect('/')->with('error', 'Unauthorized access.');
        }

        $userId = $request->session()->get('id');
        $username = $request->session()->get('username');

        // Query orders by customer_name only (customer_id doesn't exist in orders table)
        $query = DB::table('orders')->where('customer_name', $username);

        // optional date filter
        if ($request->filled('from_date') && $request->filled('until_date')) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->until_date . ' 23:59:59'
            ]);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return view('medicine-history', compact('orders'));
    }

    // API endpoint to fetch order details for receipt viewing
    public function getOrder($id)
    {
        if (!request()->session()->has('id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $username = request()->session()->get('username');
        $order = DB::table('orders')
            ->where('id', $id)
            ->where('customer_name', $username)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json($order);
    }

    // Send receipt email for historical orders
    public function sendReceiptEmailHistory(Request $request)
    {
        if (!$request->session()->has('id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $userId = $request->session()->get('id');
            
            // Get user's email from login table
            $user = DB::table('login')->where('id', $userId)->first();
            if (!$user || !$user->email) {
                return response()->json(['error' => 'User email not found'], 400);
            }
            $email = $user->email;
            
            // Validate input with better error handling
            $validated = $request->validate([
                'orderId' => 'required|integer',
                'customerName' => 'required|string',
                'orderDate' => 'required|string',
                'total' => 'required|string',
                'items' => 'required', // Can be string or array
                'status' => 'nullable|string'
            ]);

            // Parse items
            $itemsData = $validated['items'];
            if (is_string($itemsData)) {
                $items = json_decode($itemsData, true);
            } else {
                $items = $itemsData;
            }
            
            if (!is_array($items)) {
                $items = [];
            }

            // Build HTML email content
            $itemsHtml = '';
            foreach ($items as $item) {
                $name = $item['name'] ?? 'Unknown';
                $qty = $item['quantity'] ?? 1;
                $price = $item['price'] ?? 0;
                $itemsHtml .= "<tr>
                    <td>{$name}</td>
                    <td>{$qty}</td>
                    <td>\$" . number_format($price, 2) . "</td>
                </tr>";
            }

            $status = $validated['status'] ?? 'pending';
            $statusColor = $status === 'delivered' ? '#28a745' : ($status === 'cancelled' ? '#dc3545' : '#ffc107');

            $emailContent = "
            <h2>Order Receipt</h2>
            <p><strong>Customer:</strong> {$validated['customerName']}</p>
            <p><strong>Date:</strong> {$validated['orderDate']}</p>
            <h3>Order Details</h3>
            <table border='1' cellpadding='10'>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
                {$itemsHtml}
            </table>
            <h3>Total: {$validated['total']}</h3>
            <p><strong>Order Status:</strong> <span style='background-color: {$statusColor}; color: white; padding: 5px 10px; border-radius: 4px;'>" . ucfirst(str_replace('_', ' ', $status)) . "</span></p>
            <p>Thank you for your purchase!</p>";

            // Send email using Mail facade
            \Illuminate\Support\Facades\Mail::send([], [], function($message) use ($email, $emailContent) {
                $message->to($email)
                    ->subject('Order Receipt')
                    ->html($emailContent);
            });

            return response()->json(['success' => true, 'message' => 'Receipt sent to email successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed: ' . json_encode($e->errors())], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

}



