<?php

namespace App\Http\Controllers\Frontend;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\Models\CompVehicle;
use App\Models\ContVehicle;
use App\Models\DeliverySheet;
use App\Models\Driver;
use App\Models\Staff;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleAssignment;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MongoDB\Driver\Session as ses;
use RealRashid\SweetAlert\Facades\Alert;
use RealRashid\SweetAlert\SweetAlertServiceProvider;
use function JmesPath\search;
use function Sodium\compare;



class VehicleController extends Controller
{
    private $words;
    private $search;

    function __construct()
    {
        $lines = file('englishwords/nouns.txt');
        $count = 0;
        $this->words = array();
        foreach($lines as $line) {
            $count += 1;
            array_push($this->words,trim($line, " \t\n\r\0\x0B"));
        }

        parent::__construct(); //This constructor is called and now the sweet alert is working fine
    }

    public function create()
    {

        $vehicleTypes = VehicleType::all();
        $vehicle = Vehicle::with('getCompVehicle')->find(134);
        $vehicle = Vehicle::with(['getContVehicle', 'getCompVehicle', 'getVehicleType'])->find(0);
        $url = url('/frontend/add-vehicle');
        $title = "Add Vehicle";
        $data = compact('url', 'title', 'vehicle','vehicleTypes');
//        $data = compact('url', 'title');
        return view('frontend.addvehicle')->with($data);
    }


    public function view(Request $request){

        $this->search = $request['search'] ?? "";
$actual = $this->search;
        $statusView = "";
        $initialvalue = "";
        $status = "";

        if($this->search == "in:assigned " || $this->search == "in:unassigned " ){

            $status = trim( explode(":", $this->search)[1]);
            $statusView = ucfirst($status);


            $status = ucfirst($status);
            $search = $statusView;
        }



//    $vehicleObject = new VehicleController();
//    foreach ($words as $word){
//        echo $word;
//        echo "<br>";
//    }
        $arr = [];
        if($request['search']) {
            $arr = explode(" ", $request['search']);
        }


if($this->search == "Assigned" || $this->search == "Unassigned"){
    $statusView = $this->search;

}

if(count($arr)>1){
    if($arr[0] == "in:unassigned" || $arr[0] == "in:assigned"){
        $status = explode(":", $arr[0])[1];
        $statusView = ucfirst($status);

        for($i = 1; $i<count($arr); $i++){
            $initialvalue = $initialvalue .' '. $arr[$i];
        }


    }
    else{
        $initialvalue = $this->search;
    }
}else{
    $initialvalue = $this->search;
}


$this->search = $initialvalue;



if($this->search != "") {

// input misspelled word
    $input = $this->search;


// no shortest distance found, yet
    $shortest = -1;

$lev = 0;
$resultantLev = 0;
// loop through words to find the closest
    foreach ($this->words as $word) {


        // calculate the distance between the input word,
        // and the current word
        $lev = levenshtein($input, $word);

        // check for an exact match
        if ($lev == 0) {

            // closest word is this one (exact match)
            $closest = $word;
            $shortest = 0;

            // break out of the loop; we've found an exact match
            break;
        }

        // if this distance is less than the next found shortest
        // distance, OR if a next shortest word has not yet been found
        if ($lev <= $shortest || $shortest < 0) {
            // set the closest match, and shortest distance
            $closest = $word;
            $shortest = $lev;
        }
    }


//        $vehicle = Vehicle::with(['getContVehicle', 'getCompVehicle', 'getVehicleType'])->get();
//        echo "<pre>";
//        print_r($vehicle->toArray());
//        die;

    if ($closest != "" && strlen($closest) != 1 && $shortest <= 3)
    {
        $this->search = $closest;
//        echo "<div style='margin-left: 400px;'>".$shortest."'</div>";
    }
}


$search =  $this->search;



$search = trim($search, " \t\n\r\0\x0B");
//type of the vehicle should also be included in the query

        if($status != ""){

            if($search != ""){


                $vehicle = Vehicle::with(['getContVehicle', 'getCompVehicle', 'getVehicleType'])
                    ->where('deleted_at', '=', null)->where('assignStatus', '=', "$status")
                    ->where(function ($query) use($search) {
                        $query->orwhere('vehicleCode', "LIKE", "%$search%")->orwhere('plateNo', "LIKE", "%$search%")->orwhere('vehicleModel', "LIKE", "%$search%")->orwhere('condition', "LIKE", "%$search%")->orwhere('status', "LIKE", "%$search%")
                            ->orwhere('make', "LIKE", "%$search%");
                    })->orderByDesc('vehicle.created_at')
                    ->paginate(20);

                $search = $actual;
            }
            else{

                $vehicle = Vehicle::with(['getContVehicle', 'getCompVehicle', 'getVehicleType'])
                    ->where('deleted_at', '=', null)->where('assignStatus', '=', "$status")
                    ->orderByDesc('vehicle.created_at')
                    ->paginate(20);
                $search = $actual;
            }

        }else {

            if ($search != "") {

                $vehicle = Vehicle::with(['getContVehicle', 'getCompVehicle', 'getVehicleType'])
                    ->where('deleted_at', '=', null)
                    ->where(function ($query) use ($search) {
                        $query->where('assignStatus', '=', "$search")->orwhere('vehicleCode', "LIKE", "%$search%")->orwhere('plateNo', "LIKE", "%$search%")->orwhere('vehicleModel', "LIKE", "%$search%")->orwhere('condition', "LIKE", "%$search%")->orwhere('status', "LIKE", "%$search%")
                            ->orwhere('make', "LIKE", "%$search%");
                    })->orderByDesc('vehicle.created_at')
                    ->paginate(20);

            } else {
                $search = "";
                $vehicle = Vehicle::with(['getContVehicle', 'getCompVehicle', 'getVehicleType'])
                    ->where('deleted_at', '=', null)
                    ->orderByDesc('vehicle.created_at')
                    ->paginate(20);
            }
        }

        $search = $actual;



        $data = compact('vehicle', 'search', 'statusView');


        return view('frontend.viewvehicle')->with($data);
    }



    public function insert(Request $request)
    {



//        if(sizeof(DB::table('users')->where('email', $request['email'])->get()) > 0){
//            echo 'Sorry this email already exists';
//            die;
//        }



        $request->validate(
            [
                'plateNo' => 'required',
                'model' => 'required',
                'make' => 'required',
                'vehicleType' => 'required',

                'condition' => 'required',
                'ownership' => 'required',
                'price' => 'required',
                'dop' => 'required',

            ]
        );


        //in order to get the last staff_id and create a unique staffCode

        $vehicle = new Vehicle;
        $vehicle->vehicleCode = "";
        $vehicle->plateNo = strtoupper($request['plateNo']);

        $vehicle->vehicleModel = $request['model'];
        $vehicle->condition = $request['condition'];

        $vehicle->make = $request['make'];

        $vehicle->vehicleType_id = $request['vehicleType'];



        $vehicle->save();


        $id = $vehicle->vehicle_id;
        $uniqueVehicleCode = 'VH';

        if ($request['ownership'] == 'Company Owned') {
            $uniqueVehicleCode .= 'P-' .$id;
        } elseif ($request['ownership'] == 'Contractual Vehicle') {
            $uniqueVehicleCode .= 'R-' .$id;
        }

        $vehicle->vehicleCode = $uniqueVehicleCode;

        $vehicle->save();


        if ($request['ownership'] == "Company Owned") {
            $compVehicle = new CompVehicle;
            $compVehicle->vehicle_id = $id;
            $compVehicle->price = $request['price'];
            $compVehicle->purchasedDate = $request['dop'];
            $compVehicle->save();
        } elseif ($request['ownership'] == "Contractual Vehicle") {
            $contVehicle = new ContVehicle;
            $contVehicle->vehicle_id = $id;
            $contVehicle->rentPerDay = $request['price'];
            $contVehicle->dateOfContract = $request['dop'];
            $contVehicle->save();
        }



        return redirect('/frontend/vehicle/'.$vehicle->vehicle_id)->withSuccessMessage('Successfully Added!');

    }

    public function delete(Request $request){

        $id = $request->vehicle_delete_id;
        $timestamp = date("Y-m-d H:i:s");

        $vehicle = Vehicle::find($id);
        if(!is_null($vehicle)) {

//            $dSheet = DeliverySheet::where('')
            $vehicleAssignment = VehicleAssignment::find($id);
            if(!is_null($vehicleAssignment)){

                $vehicle = Vehicle::find($vehicleAssignment->vehicle_id);
                $driver = Driver::find($vehicleAssignment->driver_id);
                $driver->vhAssigned = 0;
                $driver->save();

                $vehicle->assignStatus = "Unassigned";
                $vehicle->save();
                $vehicleAssignment->delete();
            }


            $temp1 = CompVehicle::find($id);
            $temp2 = ContVehicle::find($id);
            if (!is_null($temp1)) {
                $temp1->deleted_at = $timestamp;
                $temp1->save();
            }
            elseif (!is_null($temp2)){
                $temp2->deleted_at = $timestamp;
                $temp2->save();
            }



            $dSheet = DeliverySheet::where('vehicle_id', '=', "$id")->where('status', '=', 'un-checked-out')->orderByDesc('created_at')->first();
            if(isset($dSheet->vehicle_id)){
                $dSheet->vehicle_id = null;
                $dSheet->save();
            }

            $vehicle->deleted_at = $timestamp;
            $vehicle->save();

        }


        return redirect('/frontend/view-vehicle')->withSuccessMessage('Successfully Deleted!');
    }

    public function edit($id){
//        $staff = Staff::find($id);
        $vehicleTypes = VehicleType::all();
        $vehicle = Vehicle::where('deleted_at','=',null)->with(['getContVehicle', 'getCompVehicle', 'getVehicleType'])->find($id);
        if(is_null($vehicle)) {
            return redirect('/frontend/view-vehicle');
        }else{
            $url = url('/frontend/update-vehicle').'/'.$id;
            $title = "Edit Vehicle";
            $data = compact('vehicle', 'url', 'title', 'vehicleTypes');
            return view('frontend.addvehicle')->with($data);
        }

    }


    public function update($id, Request $request){

//        date_default_timezone_set("Asia/Karachi");
//
//        $line = '';
//        $f = fopen("logFile.txt", "r") or die("Unable to open file!");
//
//        $cursor = -1;
//        fseek($f, $cursor, SEEK_END);
//        $char = fgetc($f);
////Trim trailing newline characters in the file
//        while ($char === "
//" || $char === "\r") {
//            fseek($f, $cursor--, SEEK_END);
//            $char = fgetc($f);
//        }
////Read until the next line of the file begins or the first newline char
//        while ($char !== false && $char !== "
//" && $char !== "\r") {
//            //Prepend the new character
//            $line = $char . $line;
//            fseek($f, $cursor--, SEEK_END);
//            $char = fgetc($f);
//        }
//
//        $line = substr($line, 0, -1);
//        $lineNumber = (int) $line;
//        fclose($f);
//
//        $f = fopen("logFile.txt", "a") or die("Unable to open file!");




        $request->validate(
            [
                'plateNo' => 'required',
                'model' => 'required',
                'make' => 'required',
                'vehicleType' => 'required',

                'condition' => 'required',
                'ownership' => 'required',
                'price' => 'required',
                'dop' => 'required',

            ]
        );

        $vehicle = Vehicle::with(['getContVehicle', 'getCompVehicle', 'getVehicleType'])->find($id);
//        $previousVehicleCode = $vehicle->vehicleCode;

        $vehicle->vehicleCode = "";
//        if(strcmp($vehicle->plateNo, $request['plateNo']) != 0){
//            $newLogEntry = " vehicle plateNo varchar(12) " . date_format(date_create(), 'd/m/Y H:i:s') . " " . $vehicle->plateNo . " \n" . ++$lineNumber;
//            fwrite($f, $newLogEntry);
//        }
        $vehicle->plateNo = $request['plateNo'];



        $vehicle->vehicleModel = $request['model'];
        $vehicle->condition = $request['condition'];

        $vehicle->make = $request['make'];

        $vehicle->vehicleType_id = $request['vehicleType'];

        $uniqueVehicleCode = 'VH';

        if ($request['ownership'] == 'Company Owned') {
            $uniqueVehicleCode .= 'P-' .$id;
        } elseif ($request['ownership'] == 'Contractual Vehicle') {
            $uniqueVehicleCode .= 'R-' .$id;
        }

//        if(strcmp($uniqueVehicleCode, $previousVehicleCode) != 0) {
//            $newLogEntry = " vehicle vehicleCode varchar(10) " . date_format(date_create(), 'd/m/Y H:i:s') . " " . $previousVehicleCode . " \n" .++$lineNumber;
//            fwrite($f, $newLogEntry);
//
//        }

        $vehicle->vehicleCode = $uniqueVehicleCode;

        $vehicle->save();


        if ($request['ownership'] == "Company Owned" && !isset($vehicle->getCompVehicle)) {
            $compVehicle = new CompVehicle;
            $compVehicle->vehicle_id = $id;
            $compVehicle->price = $request['price'];
            $compVehicle->purchasedDate = $request['dop'];
            $compVehicle->save();
            $contVehicle = ContVehicle::find($id);
            $contVehicle->delete();
        }elseif($request['ownership'] == "Company Owned") {
        $compVehicle = CompVehicle::find($id);
            $compVehicle->price = $request['price'];
            $compVehicle->purchasedDate = $request['dop'];
            $compVehicle->save();
        }elseif($request['ownership'] == "Contractual Vehicle" && !isset($vehicle->getContVehicle)) {
            $contVehicle = new ContVehicle;
            $contVehicle->vehicle_id = $id;
            $contVehicle->rentPerDay = $request['price'];
            $contVehicle->dateOfContract = $request['dop'];
            $contVehicle->save();
            $compVehicle = CompVehicle::find($id);
            $compVehicle->delete();
        }elseif($request['ownership'] == "Contractual Vehicle") {
            $contVehicle = ContVehicle::find($id);
            $contVehicle->rentPerDay = $request['price'];
            $contVehicle->dateOfContract = $request['dop'];
            $contVehicle->save();
        }
//        fclose($f);

        return redirect('/frontend/vehicle/'.$vehicle->vehicle_id)->withSuccessMessage('Successfully Updated!');
    }


    public function assignDriver($vehicleType): \Illuminate\Http\JsonResponse
    {

        $drivers = DB::table('staff')->select('staff.name AS stName','driver.staff_id AS staffId', 'driver.canDrive AS canDrive')->where('staff.deleted_at','=',null)
            ->join('driver', 'staff.staff_id', '=', 'driver.staff_id')->where('driver.vhAssigned','=',0)->where('canDrive', 'LIKE', "%$vehicleType%")
            ->get();
        return response()->json([
            'status' => 200,
            'drivers' => $drivers,
        ]);
    }

    public function addVehicleAssignment(Request $request){

        $vehicleId = $request['vehicleId'];
        $driverId = $request['driver'];
        $driver = Driver::find($driverId);
        $driver->vhAssigned = 1;
        $driver->save();

        //a supervisor or a manager can assign a vehicle,
        // therefore when he will be logged on to our system,
        // we can get his id and insert it in this place

        $assignedById = Session::get('staff_id');

        $vehicle = Vehicle::find($vehicleId);
        $vehicle->assignStatus = 'Assigned';
        $vehicle->save();

        $vehicleAssignment = new VehicleAssignment;
        $vehicleAssignment->vehicle_id = $vehicleId;
        $vehicleAssignment->assignedTo = $driverId;
        $vehicleAssignment->assignedBy = $assignedById;

        $vehicleAssignment->save();
        return back()->withSuccessMessage('Successfully Assigned!');

    }

    public function fetchDrivers($str): \Illuminate\Http\JsonResponse{

        $arr = explode(",", $str);

        $vehicleID = $arr[0];
        $dsID = $arr[1];


        if($vehicleID != '-1') {


            $vehicleType = DB::table('vehicle')->select('vehicle.vehicleType_id', 'vehicle_type.typeName')->where('vehicle.vehicle_id', '=', $vehicleID)
                ->leftJoin('vehicle_type', 'vehicle.vehicleType_id', '=', 'vehicle_type.vehicleType_id')->get();

            $typeName = $vehicleType[0]->typeName;

            $flag = false;

            $dSheet = DeliverySheet::find($dsID);

            if (isset($dSheet->driver_id)) {
                $driver = Driver::find($dSheet->driver_id);

                if (isset($driver)) {
                    $canDriveArr = explode(', ', $driver->canDrive);
                    if (in_array($typeName, $canDriveArr)) {
                        $flag = true;
                    }


                }

            }


            if ($flag) {
                $drivers1 = DB::table('staff')->select('staff.staffCode', 'staff.name', 'staff.staff_id')
                    ->where('staff.deleted_at', '=', null)->where('staff.staff_id', '=', $dSheet->driver_id)
                    ->get();

            }else {
                $drivers1 = DB::table('staff')->select('staff.staffCode', 'staff.name', 'staff.staff_id')
                    ->where('staff.deleted_at', '=', null)

                    ->leftJoin('vehicle_assignment', 'staff.staff_id', '=', 'vehicle_assignment.assignedTo')
                    ->where('vehicle_assignment.vehicle_id', '=', $vehicleID)->get();
                if(isset($drivers1[0])){
                    $driverTemp = Driver::find($drivers1[0]->staff_id);
                    if($driverTemp->status != "Unassigned"){
                        $drivers1 = null;
                    }
                }
            }


            $drivers = DB::table('staff')->select('staff.staffCode', 'staff.name', 'staff.staff_id')->where('staff.deleted_at', '=', null)
                ->leftJoin('driver', 'staff.staff_id', '=', 'driver.staff_id')->where('driver.status', 'Unassigned')->where('driver.canDrive', 'LIKE', "%$typeName%")->get();



            if (isset($drivers1[0])) {
                $flag = true;
                $drivers->prepend($drivers1[0]);
            }
        }else{
            $drivers = null;
        }

//WHICH ARE UNASSIGNED, should be displayed, not the one which are assigned
//        echo "<pre>";
//        print_r($drivers1);
//
//        print_r($drivers);


        return response()->json([
            'status' => 200,
            'drivers' => $drivers,

        ]);


    }


    //uPDATE THE DELIVERY SHEET ASSIGNMENT
    public function updateAssignment($str): \Illuminate\Http\JsonResponse
    {
        $arr = explode(",", $str);

        $vehicleID = $arr[0];
        $driverID = $arr[1];
        $dsID = $arr[2];



        $dSheet = DeliverySheet::find($dsID);

        if($vehicleID == "-1"){

            if(isset($dSheet->vehicle_id)) {
                $vehicle = Vehicle::find($dSheet->vehicle_id);

                $vehicle->dsAssigned = 0;
                $vehicle->status = "Idle";

                $vehicle->save();
            }

            $dSheet->vehicle_id = null;

            $dSheet->save();

            if($driverID == "-1"){

                if(isset($dSheet->driver_id)) {
                    $driver = Driver::find($dSheet->driver_id);

                    $driver->status = "Unassigned";

                    $driver->save();

                }

                $dSheet->driver_id = null;

                $dSheet->save();

            }



        }
        elseif($driverID == "-1"){

            if(isset($dSheet->driver_id)) {
                $driver = Driver::find($dSheet->driver_id);

                $driver->status = "Unassigned";

                $driver->save();
            }

            $dSheet->driver_id = null;

            $dSheet->save();


            if($vehicleID == "-1") {

                if(isset($dSheet->vehicle_id)) {
                    $vehicle = Vehicle::find($dSheet->vehicle_id);

                    $vehicle->dsAssigned = 0;
                    $vehicle->status = "Idle";

                    $vehicle->save();

                }
                $dSheet->vehicle_id = null;

                $dSheet->save();
            }


        }
        else {


            $dSheetTemp = DeliverySheet::where('vehicle_id', '=', "$vehicleID")->where('deliverySheet_id', '!=', "$dsID")->where('status', '=', 'un-checked-out')->get();

            $dSheetTemp2 = DeliverySheet::where('driver_id', '=', "$driverID")->where('deliverySheet_id', '!=', "$dsID")->where('status', '=', 'un-checked-out')->get();

            if (count($dSheetTemp) > 0) {
                foreach ($dSheetTemp as $deliverySheet) {

                    $deliverySheet->vehicle_id = null;
                    $deliverySheet->save();
                }
            }

            if (count($dSheetTemp2) > 0) {
                foreach ($dSheetTemp2 as $deliverySheet2) {
                    $deliverySheet2->driver_id = null;
                    $deliverySheet2->save();
                }
            }

            if (isset($dSheet->vehicle_id)) {

                $vehicle = Vehicle::find($dSheet->vehicle_id);
                $vehicle->dsAssigned = 0;
                $vehicle->status = 'Idle';
                $vehicle->save();
            }

            if (isset($dSheet->driver_id)) {

                $driver = Driver::find($dSheet->driver_id);
                $driver->status = 'Unassigned';
                $driver->save();
            }


            $dSheet->driver_id = $driverID;
            $dSheet->vehicle_id = $vehicleID;
            $dSheet->save();

            $vehicleNew = Vehicle::find($dSheet->vehicle_id);
            $vehicleNew->dsAssigned = 1;

            $vehicleNew->save();


            $driver = Driver::find($dSheet->driver_id);
            $driver->status = 'Assigned';
            $driver->save();


//        return redirect('/frontend/view-deliverysheet/'.$dsID)->withSuccessMessage('Successfully Updated!');

        }


        return response()->json([
                'status' => 200,
            ]);


    }


    public function viewSingle($id){



        $vehicle = Vehicle::find($id);


        $vehicleData = Vehicle::with(['getContVehicle', 'getCompVehicle', 'getVehicleType'])->where('vehicle.vehicle_id',"$id")->first();



        if(isset($vehicle->vehicleType_id)){
            $vehicleType  = VehicleType::find($vehicle->vehicleType_id);
        }else{
            $vehicleType = null;
        }

        $vehicleAssignment = DB::table('vehicle_assignment')->where('vehicle_id','=', "$id")->first();




if(isset($vehicleAssignment->assignedTo)) {
    $driver = Staff::find($vehicleAssignment->assignedTo);
    $supervisor = Staff::find($vehicleAssignment->assignedBy);
}else{
    $driver = null;
    $supervisor = null;
}

        $dSheet = DB::table('delivery_sheet')->where('finished', 0)->where('vehicle_id','=', "$id")->orderByDesc('created_at')->first();


        if(!isset($vehicle)){
            return redirect('/frontend/view-vehicle')->withErrorMessage('Sorry no record found!');
        }

        $data = compact( 'vehicle', 'driver','dSheet','vehicleData', 'vehicleType','supervisor', 'vehicleAssignment');
//        $data = compact('url', 'title');

        return view('frontend.viewsinglevehicle')->with($data);


    }


//
//    public function validateEmail($str){
//
//        $email = $str;
//
//        if(User::find($email))
//
//
//    }


}
