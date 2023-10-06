<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserCard;
use App\Models\Card;
use App\Models\Language;
use App\Models\ConsentIdType;
use App\Models\ConsentId;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function generateRandom()
    {
        $randomBytes = random_bytes(15);
        $random = bin2hex($randomBytes);
        return $random;
    }
    public function isExist()
    {
        $random = $this->generateRandom();
        $exist = ConsentIdType::where('type', $random)->count();
        return [$exist, $random];
    }

    public function generateString()
    {
        $isExist = $this->isExist();
        if ($isExist[0] > 0) {
            $this->generateString();
        } else {
            return $isExist[1];
        }
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name = $request->name;
        $phone = $request->phone;
        $password = $request->password;
        $consent_id1 = $request->consent_id1;
        $consent_id2 = $request->consent_id2;
        $consent_id3 = $request->consent_id3;
        $id_card = $request->id_card;
        $language = $request->language;
        $name_level = $request->name_level;

        if ($consent_id1) {
            $consent_id1Validate = $this->generateString();
        } else {
            $responseData = [
                'Error' => 'Campo consent_id1 false'
            ];
            return response()->json($responseData, 500);
        }
        User::insert(
            [
                'name' => $name,
                'phone' => $phone,
                'password' => $password,
                'created_at' => now()
            ]
        );
        $user = User::orderBy('id', 'DESC')->first();
        $id_user = $user->id;

        Card::insert(
            [
                'id_card' => $id_card,
                'created_at' => now()
            ]
        );
        $card = Card::orderBy('id', 'DESC')->first();
        $card = $card->id;
        Language::insert(
            [
                'name' => $language,
                'created_at' => now()
            ]
        );
        $languages = Language::orderBy('id', 'DESC')->first();
        $id_lenguages = $languages->id;

        $id_consent_id_type = ConsentIdType::orderBy('id', 'DESC')->first();
        $id_consent_id_types = $id_consent_id_type->id;
        ConsentId::insert(
            [
                'name' => $consent_id1Validate,
                'name_lavel' => $name_level,
                'id_consent_id_types' => 1,
                'created_at' => now()
            ],
            [
                'name' => $this->generateRandom(),
                'name_lavel' => $name_level,
                'id_consent_id_types' => 2,
                'created_at' => now()
            ],
            [
                'name' => $this->generateRandom(),
                'name_lavel' => $name_level,
                'id_consent_id_types' => 2,
                'created_at' => now()
            ],
        );
        $id_consent_id = ConsentId::orderBy('id', 'DESC')->first();
        $id_consent = $id_consent_id->id;
        $lastRegisters = ConsentId::latest()->take(3)->get();


        UserCard::insert(
            [
                'id_user' => $id_user,
                'id_card' => $card,
                'id_lenguages' => $id_lenguages,
                'consent_id1' => $lastRegisters[2]->id,
                'consent_id2' => $lastRegisters[1]->id,
                'consent_id3' => $lastRegisters[0]->id,
                'created_at' => now()
            ]
        );
        $responseData = [
            'success' => true
        ];

        return response()->json($responseData, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $idConsent = UserCard::select('consent_id2', 'consent_id3')->where('id_user', $id)->get();
            $consent2 = ConsentId::find($idConsent[0]->consent_id2);
            $consent2->update([
                'name' => $this->generateRandom(),
                'updated_at' => now(),
            ]);
            $consent3 = ConsentId::find($idConsent[0]->consent_id3);
            $consent3->update([
                'name' => $this->generateRandom(),
                'updated_at' => now(),
            ]);

            $user = User::find($id);
            $user->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'password' => $request->password,
                'updated_at' => now(),
            ]);

            $responseData = [
                'success' => true
            ];
            return response()->json($responseData, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Ocurrio un error'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


        $user = UserCard::where('id_user',$id);
        $user->delete();
        $user = User::find($id);
        $user->delete();
        $responseData = [
            'success' => true
        ];
        return response()->json($responseData, 200);
    }
}
