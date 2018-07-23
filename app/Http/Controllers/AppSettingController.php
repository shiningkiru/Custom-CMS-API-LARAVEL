<?php

namespace App\Http\Controllers;
use Validator;
use App\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;


class AppSettingController  extends Controller
{


    /**
     * @SWG\Post(
     *      path="/app-setting",
     *      operationId="setting-store",
     *      tags={"Setting"},
     *      summary="Store main app settings",
     *      description="Returns list of menus",
     *      @SWG\Parameter(
     *          name="companyName",
     *          description="Name of the company",
     *          required=false,
     *          type="string",
     *          in="formData"
     *      ),
     *      @SWG\Parameter(
     *          name="description",
     *          description="description of the company",
     *          required=false,
     *          type="string",
     *          in="formData"
     *      ),
     *      @SWG\Parameter(
     *          name="primaryPhone",
     *          description="Primary phone number of the company",
     *          required=false,
     *          type="string",
     *          in="formData"
     *      ),
     *      @SWG\Parameter(
     *          name="secondaryPhone",
     *          description="Secondary  phone number of the company",
     *          required=false,
     *          type="string",
     *          in="formData"
     *      ),
     *      @SWG\Parameter(
     *          name="primaryEmail",
     *          description="Primary Email of the company",
     *          required=false,
     *          type="string",
     *          in="formData"
     *      ),
     *      @SWG\Parameter(
     *          name="secondaryEmail",
     *          description="Secondary email of the company",
     *          required=false,
     *          type="string",
     *          in="formData"
     *      ),
     *      @SWG\Parameter(
     *          name="primaryAddress",
     *          description="Primary address of the company",
     *          required=false,
     *          type="string",
     *          in="formData"
     *      ),
     *      @SWG\Parameter(
     *          name="secondaryAddress",
     *          description="Secondary address of the company",
     *          required=false,
     *          type="string",
     *          in="formData"
     *      ),
     *      @SWG\Parameter(
     *          name="facebookLink",
     *          description="Facebook link of the company",
     *          required=false,
     *          type="string",
     *          in="formData"
     *      ),
     *      @SWG\Parameter(
     *          name="twitterLink",
     *          description="Twitter link of the company",
     *          required=false,
     *          type="string",
     *          in="formData"
     *      ),
     *      @SWG\Parameter(
     *          name="instaLink",
     *          description="Instagram link of the company",
     *          required=false,
     *          type="string",
     *          in="formData"
     *      ),
     *      @SWG\Parameter(
     *          name="googleLink",
     *          description="Google link of the company",
     *          required=false,
     *          type="string",
     *          in="formData"
     *      ),
     *      @SWG\Parameter(
     *          name="whatsAppLink",
     *          description="Whatsapp link of the company",
     *          required=false,
     *          type="string",
     *          in="formData"
     *      ),
     *      @SWG\Parameter(
     *          name="youtubeLink",
     *          description="Youtube link of the company",
     *          required=false,
     *          type="string",
     *          in="formData"
     *      ),
     *      @SWG\Parameter(
     *          name="footerMessage",
     *          description="Copyright message of footer",
     *          required=false,
     *          type="string",
     *          in="formData"
     *      ),
     *      @SWG\Parameter(
     *          name="embedMap",
     *          description="Embed map code for the site",
     *          required=false,
     *          type="string",
     *          in="formData"
     *      ),
     *      @SWG\Parameter(
     *          name="longitude",
     *          description="Longitude of company location",
     *          required=false,
     *          type="string",
     *          in="formData"
     *      ),
     *      @SWG\Parameter(
     *          name="latitude",
     *          description="Latitude of company location",
     *          required=false,
     *          type="string",
     *          in="formData"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=500, description="Internal server error"),
     *       @SWG\Response(response=400, description="Bad request"),
     *     )
     *
     * Returns list of menus
     */
    public function store(Request $request)
    {
      $validatedData = Validator::make($request->all(),[
            'primaryEmail' => 'required',
            'secondaryEmail' => 'required'
        ]);
        if ($validatedData->fails()) {
            return response()->json($validatedData->errors(),400);
        }

        $setting = AppSetting::find(999);
        if(!($setting instanceof AppSetting)){
            $setting=new AppSetting();
            $setting->id = 999;
        }
        $setting->companyName=$request->companyName;
        $setting->description=$request->description;
        $setting->primaryPhone=$request->primaryPhone;
        $setting->secondaryPhone=$request->secondaryPhone;
        $setting->primaryEmail=$request->primaryEmail;
        $setting->secondaryEmail=$request->secondaryEmail;
        $setting->primaryAddress=$request->primaryAddress;
        $setting->secondaryAddress=$request->secondaryAddress;
        $setting->facebookLink=$request->facebookLink;
        $setting->twitterLink=$request->twitterLink;
        $setting->instaLink=$request->instaLink;
        $setting->googleLink=$request->googleLink;
        $setting->whatsAppLink=$request->whatsAppLink;
        $setting->youtubeLink=$request->youtubeLink;
        $setting->footerMessage=$request->footerMessage;
        $setting->embedMap=$request->embedMap;
        $setting->longitude=$request->longitude;
        $setting->latitude=$request->latitude;

        
        if($request->file('logo') instanceof UploadedFile):
                $imageName=null;
                $image = $request->file('logo');
                $imageName = uniqid().'main.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/main');
                $image->move($destinationPath, $imageName);
                $imageName = "/uploads/main/".$imageName;
                $setting->logo= $imageName;
        endif;

        $setting->save();
        return response()->json("DATA UPDATED", 200); 
    }

    
    /**
     * @SWG\Get(
     *      path="/app-setting",
     *      operationId="setting-index",
     *      tags={"Setting"},
     *      summary="Get saved settings",
     *      description="Returns saved settings",
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @SWG\Response(response=400, description="Bad request")
     *     )
     *
     * Returns setting details
     */
    public function index(){
        $setting= AppSetting::find(999);
        return response()->json($setting, 200);
    }
}