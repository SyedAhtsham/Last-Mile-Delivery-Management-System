@extends('frontend.layouts.main')
@section('main-container')

    <script>
        /* If browser back button was used, flush cache */
        // (function () {
        //     window.onpageshow = function(event) {
        //         if (event.persisted) {
        //             window.location.reload();
        //         }
        //     };
        // })();

    </script>


    <div class="container pt-5">


    </div>
    <div class="main">


        <h4>{{$title}}</h4>

        <hr>
        <br>
        <form action="{{$url}}" method="post">
            @csrf

            <div class="form-row pr-5">
                <div class="form-group col-md-4 pr-5">
                    <label for="inputName4">Name <span class="required">*</span></label>
                    <input type="text" name="name" value="{{$staff->name ?? old('name')}}" class="form-control"
                           id="inputName4" placeholder="e.g., Muhammad Ali"
                           value="{{old('name')}}" required>
                    <span class="text-danger">
                    @error('name')
                        {{$message}}
                        @enderror
                </span>
                </div>
                <div class="form-group col-md-4 pr-5">
                    <label for="input">Contact <span class="required">*</span></label>
                    <input type="text" max="14" name="contact" value="{{$staff->contact ?? old('contact')}}"
                           class="form-control" id="inputContact"
                           placeholder="e.g., 0315-52432142" value="{{old('contact')}}" required>
                    <span class="text-danger">
                    @error('contact')
                        {{$message}}
                        @enderror
                </span>
                </div>
                <div class="form-group col-md-4 pr-5">
                    <label for="input">CNIC <span class="required">*</span></label>
                    <input type="text" max="14" name="cnic" value="{{$staff->cnic ?? old('cnic')}}" class="form-control"
                           id="inputCNIC"
                           placeholder="e.g., 6110123234565" value="{{old('cnic')}}" required>
                    <span class="text-danger">
                    @error('cnic')
                        {{$message}}
                        @enderror
                </span>
                </div>

            </div>
            <div class="form-row pr-5 pt-3">
                <div class="form-group col-md-4 pr-5">
                    <label for="inputEmail4">Email <span class="required">*</span></label>
                    <input type="email" name="email" value="{{$staff->email ?? old('email')}}" class="form-control"
                           id="inputEmail4"
                           placeholder="e.g., mbhuralqau@gmail.com" required>
                    <span class="text-danger">
                    @error('email')
                        {{$message}}
                        @enderror
                </span>
                </div>

                <div class="form-group col-md-4 pr-5">
                    <label for="inputDOB">Date of Birth</label>
                    <input type="date" name="dob" max="2022-09-28" class="form-control" id="inputDOB"
                           value="{{$staff->dob ?? old('dob')}}"
                           placeholder="">
                    <span class="text-danger">
                    @error('dob')
                        {{$message}}
                        @enderror
                </span>
                </div>
                <div class="form-group col-md-4 pr-5">

                    <label for="inputGender">Gender <span class="required">*</span></label>
                    <div class="d-flex">
                        <input type="radio" name="gender" checked id="inputGender" value="Male"

                        @if(isset($staff->gender))
                            {{$staff->gender == "Male" ? "checked" : ""}}

                            @else
                            {{old('gender') == "Male" ? "checked" : ""}}

                            @endif
                        >
                        <label style="padding-left: 5px; padding-right: 12px; padding-top: 10px;">Male</label>
                        <input type="radio" name="gender" id="inputGender" value="Female"
                        @if(isset($staff->gender))
                            {{$staff->gender == "Female" ? "checked" : ""}}
                            @else
                            {{old('gender') == "Female" ? "checked" : ""}}
                            @endif
                        > <label
                            style="padding-left: 5px; padding-right: 12px; padding-top: 10px;">Female</label>
                        <input type="radio" name="gender" id="inputGender" value="Other"
                        @if(isset($staff->gender))
                            {{$staff->gender == "Other" ? "checked" : ""}}
                            @else
                            {{old('gender') == "Other" ? "checked" : ""}}
                            @endif
                        > <label
                            style="padding-left: 5px; padding-right: 12px; padding-top: 10px;">Other</label>
                    </div>
                    <span class="text-danger">
                    @error('gender')
                        {{$message}}
                        @enderror
                </span>
                </div>
            </div>

            <div class="form-row pr-5 pt-3">
                <div class="form-group col-4 pr-5">
                    <label for="inputPosition">Position <span class="required">*</span></label>
                    <select id="inputPosition" onchange="showForDriver(this)" name="position" class="form-control"
                            required>
                        @php
                            $pos = old('position') ?? null;
                        @endphp

                        <option value="Manager"

                        @if(isset($pos))
                            {{$pos == "Manager" ? "selected" : ""}}
                            @elseif(isset($staff->position))
                            {{$staff->position == "Manager" ? "selected" : ""}}
                            @endif
                        >Manager
                        </option>
                        <option value="Driver"
                        @if(isset($pos))
                            {{$pos == "Driver" ? "selected" : ""}}
                            @elseif(isset($staff->position))
                            {{$staff->position == "Driver" ? "selected" : ""}}
                            @endif
                        >Driver
                        </option>
                        <option value="Supervisor"
                        @if(isset($pos))
                            {{$pos == "Supervisor" ? "selected" : ""}}
                            @elseif(isset($staff->position))
                            {{$staff->position == "Supervisor" ? "selected" : ""}}
                            @endif
                        >Supervisor
                        </option>

                    </select>
                    <span class="text-danger">
                    @error('position')
                        {{$message}}
                        @enderror
                </span>
                </div>
                <div class="form-group col-8 pr-5">
                    <label for="inputAddress">Address <span class="required">*</span></label>
                    <input type="text" name="address" class="form-control" id="inputAddress"
                           value="{{$staff->address ?? old('address')}}"
                           placeholder="e.g., 84 Hostel 6 QAU" required>
                    <span class="text-danger">
                    @error('address')
                        {{$message}}
                        @enderror
                </span>
                </div>


            </div>

            @php
                $position = "";
                if(isset($staff->position)){
                $position = $staff->position;
                }

                 if(old('canDrive[]') !== null)
                    {
                        echo "Shah";
                        $oldCanDrive[] = old('canDrive[]');
                    }

                 if(isset($staff->getDriver->canDrive)){
                     $canDriveArr = explode(', ', $staff->getDriver->canDrive);

                 }
            @endphp

            @if($position == "Driver" || old('position') == "Driver")
                <body onload="myFunction1()">
                <div id="ifYes1" class="form-row pr-5 pt-3">
                    <div class="form-group col-md-4 pr-5">
                        <label for="inputLicenseNo0">License No. <span class="required">*</span></label>
                        <input id="inputLicenseNo0" type="text" name="licenseNo" class="form-control"
                               value="{{$staff->getDriver->licenseNo ?? old('licenseNo')}}"
                               placeholder="e.g., LE165487" required>
                        <span class="text-danger">
                    @error('licenseNo')
                            {{$message}}
                            @enderror
                </span>
                    </div>
                    <div class="form-group col-md-4 pr-5">
                        <label for="inputYearsExperience0">Years Experience</label>

                        <input type="number" name="yearsExperience" class="form-control" id="inputYearsExperience0"
                               value="{{$staff->getDriver->yearsExp ?? old('yearsExperience')}}" placeholder="e.g., 1">
                    </div>
                    <div class="form-group col-4 pr-5">
                        <label for="inputCanDrive0">Can Drive <span class="required">*</span></label>
                        <select id="inputCanDrive0" name="canDrive[]" multiple class="form-control selectpicker">

                            @foreach($vehicleTypes as $vT)
                                <option value={{$vT->typeName}}


                                @if(isset($staff->getDriver->canDrive))
                                    {{in_array($vT->typeName, $canDriveArr) ? "selected" : ""}}
                                    @else
                                    {{--                                            {{$oldCanDrive.contains($vT->vehicleType_id)  ? "selected" : ""}}--}}

                                    @endif

                                >{{$vT->typeName}}
                                </option>
                            @endforeach

                        </select>
                        <span class="text-danger">
                    @error('canDrive')
                            {{$message}}
                            @enderror
                </span>
                    </div>

                </div>

                @else
                    <body onload="myFunction()">

                    @endif


                    <div id="ifYes" class="form-row pr-5 pt-3">
                        <div class="form-group col-md-4 pr-5">
                            <label for="inputLicenseNo">License No. <span class="required">*</span></label>
                            <input id="licenseNo1" type="text" name="licenseNo" class="form-control"
                                   value="{{$staff->getDriver->licenseNo ?? old('licenseNo')}}"
                                   placeholder="e.g., LE165487" required>
                            <span class="text-danger">
                    @error('licenseNo')
                                {{$message}}
                                @enderror
                </span>
                        </div>
                        <div class="form-group col-md-4 pr-5">
                            <label for="inputYearsExperience">Years Experience</label>
                            <input type="number" name="yearsExperience" class="form-control" id="inputYearsExperience1"
                                   value="{{$staff->getDriver->yearsExp ?? old('yearsExperience')}}"
                                   placeholder="e.g., 1">
                        </div>
                        <div class="form-group col-4 pr-5">
                            <label for="inputCanDrive1">Can Drive <span class="required">*</span></label>
                            <select id="inputCanDrive1" name="canDrive[]" multiple class="form-control selectpicker">

                                @foreach($vehicleTypes as $vT)
                                    <option value={{$vT->typeName}}


                                    @if(isset($staff->getDriver->canDrive))
                                        {{$staff->getDriver->canDrive == $vT->typeName ? "selected" : ""}}
                                        @else
                                        {{--                                            {{$oldCanDrive.contains($vT->vehicleType_id)  ? "selected" : ""}}--}}

                                        @endif

                                    >{{$vT->typeName}}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger">
                    @error('canDrive')
                                {{$message}}
                                @enderror
                </span>
                        </div>

                    </div>


                    <div id="ifYes2" class="form-row pr-5 pt-3">
                        <div class="form-group col-md-4 pr-5">
                            <label for="licenseNo">License No. <span class="required">*</span></label>
                            <input id="licenseNo" type="text" name="licenseNo" class="form-control"
                                   value="{{$staff->getDriver->licenseNo ?? old('licenseNo')}}"
                                   placeholder="e.g., LE165487" required>
                            <span class="text-danger">
                    @error('licenseNo')
                                {{$message}}
                                @enderror
                </span>
                        </div>
                        <div class="form-group col-md-4 pr-5">
                            <label for="inputYearsExperience2">Years Experience</label>
                            <input type="number" name="yearsExperience" class="form-control" id="inputYearsExperience2"
                                   value="{{$staff->getDriver->yearsExp ?? old('yearsExperience')}}"
                                   placeholder="e.g., 1">
                        </div>


                        <div class="form-group col-4 pr-5">
                            <label for="inputCanDrive2">Can Drive <span class="required">*</span></label>
                            <select id="inputCanDrive2" name="canDrive[]" multiple class="form-control selectpicker"
                                    data-live-search="true">

                                @foreach($vehicleTypes as $vT)
                                    <option value={{$vT->typeName}}


                                        @if(isset($staff->getDriver->canDrive))
                                            {{$staff->getDriver->canDrive == $vT->typeName ? "selected" : ""}}
                                        @else
{{--                                            {{$oldCanDrive.contains($vT->vehicleType_id)  ? "selected" : ""}}--}}

                                        @endif

                                    >{{$vT->typeName}}
                                    </option>
                                @endforeach


                            </select>
                            <span class="text-danger">
                    @error('position')
                                {{$message}}
                                @enderror
                </span>
                        </div>
                    </div>


                    <div class="form-row pt-5">

                        <div>
                            <a href="{{url('/frontend/view-staff')}}">
                                <button id="cancel" type="button" style="width: 8em; margin-right: 2em;"
                                        class="btn btn-danger">Cancel
                                </button>
                            </a>
                        </div>
                        <div>

                            <button type="submit"
                                    style="width: 8em; margin-right: 2em;  background-color: rgb(0, 74, 111);"
                                    class="btn btn-primary">Submit
                            </button>
                        </div>

                    </div>

        </form>
    </div>

@endsection



