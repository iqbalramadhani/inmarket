<form class="form-default" role="form" action="{{ route('addresses.update', $address_data->id) }}" method="POST">
    @csrf
    <div class="p-3">
        <div class="row">
            <div class="col-md-2">
                <label>{{ translate('Address')}}</label>
            </div>
            <div class="col-md-10">
                <textarea class="form-control mb-3" placeholder="{{ translate('Your Address')}}" rows="2" name="address" required>{{ $address_data->address }}</textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <label>{{ translate('Country')}}</label>
            </div>
            <div class="col-md-10">
                <div class="mb-3">
                    <select class="form-control aiz-selectpicker" data-live-search="true" data-placeholder="{{ translate('Select your country')}}" name="country" id="edit_country" required>
                        @foreach (\App\Country::where('status', 1)->get() as $key => $country)
                        <option value="{{ $country->name }}" @if($address_data->country == $country->name) selected @endif>
                            {{ $country->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div id="no-overseas">
            <div class="row">
                <label class="col-md-2 col-form-label">{{ translate('Province') }}</label>
                <div class="col-md-10">
                    <div class="mb-3">
                        <select class="form-control aiz-selectpicker" data-live-search="true" data-placeholder="{{translate('Select your province')}}" name="province" required
                        @if($address_data->country!='Indonesia') disabled @endif
                        >
                            <option value="" selected disabled>Select Province</option>
                            @if($address_data->country=='Indonesia')
                            @foreach($locations['provinces'] as $key => $item)
                            <option value="{{$item->id}}"
                                @if($address_data->province == $item->name) selected
                                @endif
                                >{{$item->name}}
                            </option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <label>{{ translate('City')}}</label>
                </div>
                <div class="col-md-10">
                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="city"

                    @if($address_data->country!='Indonesia') disabled @endif
                    >
                        <option value="" selected disabled>Select City</option>
                        @foreach($locations['cities'] as $key => $item)
                            <option value="{{$item->id}}"
                                @if($address_data->city == $item->name) selected
                                @endif
                            >{{$item->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <label>{{ translate('District')}}</label>
                </div>
                <div class="col-md-10">
                    <select class="form-control mb-3 aiz-selectpicker" name="district"
                    @if($address_data->country!='Indonesia') disabled @endif
                    >
                        <option value="" selected disabled>Select District</option>
                        @foreach($locations['districts'] as $key => $item)
                            <option value="{{$item->id}}"
                                @if($address_data->district == $item->name) selected
                                @endif
                            >{{$item->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <label>{{ translate('Sub District')}}</label>
                </div>
                <div class="col-md-10">
                    <select class="form-control mb-3 aiz-selectpicker" name="sub_district"
                    @if($address_data->country!='Indonesia') disabled @endif
                    >
                        <option value="" selected disabled>Select Province</option>
                        @foreach($locations['sub_districts'] as $key => $item)
                            <option value="{{$item->id}}"
                                @if($address_data->sub_district == $item->name) selected
                                @endif
                            >{{$item->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <label>{{ translate('Postal Code')}}</label>
                </div>
                <div class="col-md-10">
                    <select class="form-control mb-3 aiz-selectpicker" name="postal_code"
                    @if($address_data->country!='Indonesia') disabled @endif
                    >
                        <option value="" selected disabled>Select Province</option>
                        @foreach($locations['postal_codes'] as $key => $item)
                            <option value="{{$item->id}}"
                                @if($address_data->postal_code == $item->kodepos) selected
                                @endif
                            >{{$item->kodepos}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div id="overseas" class="row">
            <div class="col-md-2">
                <label>{{ translate('Detail')}}</label>
            </div>
            <div class="col-md-10">
                <textarea class="form-control mb-3 aiz-selectpicker" name="detail" >{{$address_data->detail}}</textarea>
            </div>
        </div>

        <!-- <div class="row">
            <div class="col-md-2">
                <label>{{ translate('City')}}</label>
            </div>
            <div class="col-md-10">
                <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="city" required>

                </select>
            </div>
        </div> -->

        @if (get_setting('google_map') == 1)
            <div class="row">
                <input id="edit_searchInput" class="controls" type="text" placeholder="Enter a location">
                <div id="edit_map"></div>
                <ul id="geoData">
                    <li style="display: none;">Full Address: <span id="location"></span></li>
                    <li style="display: none;">Postal Code: <span id="postal_code"></span></li>
                    <li style="display: none;">Country: <span id="country"></span></li>
                    <li style="display: none;">Latitude: <span id="lat"></span></li>
                    <li style="display: none;">Longitude: <span id="lon"></span></li>
                </ul>
            </div>

            <div class="row">
                <div class="col-md-2" id="">
                    <label for="exampleInputuname">Longitude</label>
                </div>
                <div class="col-md-10" id="">
                    <input type="text" class="form-control mb-3" id="edit_longitude" name="longitude" value="{{ $address_data->longitude }}" readonly="">
                </div>
            </div>
            <div class="row">
                <div class="col-md-2" id="">
                    <label for="exampleInputuname">Latitude</label>
                </div>
                <div class="col-md-10" id="">
                    <input type="text" class="form-control mb-3" id="edit_latitude" name="latitude" value="{{ $address_data->latitude }}" readonly="">
                </div>
            </div>
        @endif

        <!-- <div class="row">
            <div class="col-md-2">
                <label>{{ translate('Postal code')}}</label>
            </div>
            <div class="col-md-10">
                <input type="text" class="form-control mb-3" placeholder="{{ translate('Your Postal Code')}}" value="{{ $address_data->postal_code }}" name="postal_code" value="" required>
            </div>
        </div> -->
        <div class="row">
            <div class="col-md-2">
                <label>{{ translate('Phone')}}</label>
            </div>
            <div class="col-md-10">
                <input type="text" class="form-control mb-3" placeholder="{{ translate('+880')}}" value="{{ $address_data->phone }}" name="phone" value="" required>
            </div>
        </div>
        <div class="form-group text-right">
            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
        </div>
    </div>
</form>
