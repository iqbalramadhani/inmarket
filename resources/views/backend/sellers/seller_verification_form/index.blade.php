@extends('backend.layouts.app')

@section('content')

	<div class="col-sm-12">

		<div class="card">
			<div class="card-header">
				<h5 class="mb-0 h6">{{translate('Seller Verification Form')}}</h5>
			</div>
			<div class="card-body">
				<form action="{{ route('seller_verification_form.update') }}" method="post">
					@csrf
					<div class="row">
						<div class="col-lg-8 form-horizontal" id="form">
							@php $is_location = false; @endphp
							@foreach (json_decode(get_setting('verification_form')) as $key => $element)
								@if ($element->type == 'text' || $element->type == 'file')
									<div class="form-group row" style="background:rgb(227 227 227 / 25%);padding:10px 0;">
									    <input type="hidden" name="type[]" value="{{ $element->type }}">
									    <div class="col-lg-3">
									        <label class="col-from-label">{{ ucfirst($element->type) }}</label>
									    </div>
									    <div class="col-lg-7">
									        <input class="form-control" type="text" name="label[]" value="{{ $element->label }}" placeholder="{{ translate('Label') }}">
									    </div>
									    <div class="col-lg-2"><span class="btn btn-icon btn-circle icon-lg" onclick="delete_choice_clearfix(this)"><i class="las la-times"></i></span></div>

										@if (!empty($element->is_required))
											<div class="col-lg-3">
												<label class="col-from-label">{{translate('Required')}}</label>
											</div>
											<div class="col-lg-7">															
													<select class="form-control aiz-selectpicker" name="required[]">
														<option value="false" {{ $element->is_required == 'false' ? "selected" : "" }}> Not Required</option>
                                                        <option value="true" {{ $element->is_required == 'true' ? "selected" : "" }}> Required </option>
													</select>
											</div>
										@else
											<div class="col-lg-3">
												<label class="col-from-label">{{translate('Required')}}</label>
											</div>
											<div class="col-lg-7">															
													<select class="form-control aiz-selectpicker" name="required[]">
														<option value="false"> Not Required</option>
                                                        <option value="true"> Required </option>
													</select>
											</div>
										@endif
									</div>
									
									
								@elseif ($element->type == 'select' || $element->type == 'multi_select' || $element->type == 'radio')
									<div class="form-group row" style="background:rgb(227 227 227 / 25%);padding:10px 0;">
									    <input type="hidden" name="type[]" value="{{ $element->type }}">
									    <input type="hidden" name="option[]" class="option" value="{{ $key }}">
									    <div class="col-lg-3">
									        <label class="col-from-label">{{ ucfirst(str_replace('_', ' ', $element->type)) }}</label>
									    </div>
									    <div class="col-lg-7">
									        <input class="form-control" type="text" name="label[]" value="{{ $element->label }}" placeholder="{{ translate('Select Label') }}" style="margin-bottom:10px">
									        <div class="customer_choice_options_types_wrap_child">
												@if (is_array(json_decode($element->options)))
													@foreach (json_decode($element->options) as $value)
														<div class="form-group row">
														    <div class="col-sm-6 col-sm-offset-4">
														        <input class="form-control" type="text" name="options_{{ $key }}[]" value="{{ $value }}" required="">
														    </div>
														    <div class="col-sm-2"> <span class="btn btn-icon btn-circle icon-lg" onclick="delete_choice_clearfix(this)"><i class="las la-times"></i></span></div>
														</div>
													@endforeach
												@endif
											</div>
									        <button class="btn btn-success pull-right" type="button" onclick="add_customer_choice_options(this)"><i class="glyphicon glyphicon-plus"></i> Add option</button>
									    </div>
									    <div class="col-lg-2"><span class="btn btn-icon btn-circle icon-lg" onclick="delete_choice_clearfix(this)"><i class="las la-times"></i></span></div>
										
										@if (!empty($element->is_required))
											<div class="col-lg-3">
													<label class="col-from-label">{{translate('Required')}}</label>
											</div>
											<div class="col-lg-7">															
													<select class="form-control aiz-selectpicker" name="required[]">
														<option value="false" {{ $element->is_required == 'false' ? "selected" : "" }}> Not Required</option>
														<option value="true" {{ $element->is_required == 'true' ? "selected" : "" }}> Required </option>
													</select>
											</div>
										@else
											<div class="col-lg-3">
												<label class="col-from-label">{{translate('Required')}}</label>
											</div>
											<div class="col-lg-7">															
													<select class="form-control aiz-selectpicker" name="required[]">
														<option value="false"> Not Required</option>
                                                        <option value="true"> Required </option>
													</select>
											</div>
										@endif
									</div>
								@elseif ($element->type == 'location')
									@php $is_location = true; @endphp
									<div class="form-group row" style="background:rgb(227 227 227 / 25%);padding:10px 0;">
									<input type="hidden" name="type[]" value="location">
									<input type="hidden" name="label[]" value="location">
									<div class="col-lg-3">
										<label class="col-from-label">Location</label>
									</div>
									
									<div class="col-lg-7">
										<input class="form-control" type="text" name="options_location[]" value="Provinsi" disabled>
									</div>
									<div class="col-lg-2">
										<span class="btn btn-icon btn-circle icon-lg" onclick="delete_choice_clearfix(this)"><i class="las la-times"></i></span>
									</div>

									<div class="col-lg-3">
										<label class="col-from-label"></label>
									</div>
									<div class="col-lg-7">
										<input class="form-control" type="text" name="options_location[]" value="Kota" disabled>
									</div>

									<div class="col-lg-3">
										<label class="col-from-label"></label>
									</div>
									<div class="col-lg-7">
										<input class="form-control" type="text" name="options_location[]" value="Kecamatan" disabled>
									</div>

									<div class="col-lg-3">
										<label class="col-from-label"></label>
									</div>
									<div class="col-lg-7">
										<input class="form-control" type="text" name="options_location[]" value="Kelurahan" disabled>
									</div>

									<div class="col-lg-3">
										<label class="col-from-label"></label>
									</div>
									<div class="col-lg-7">
										<input class="form-control" type="text" name="options_location[]" value="Kode Pos" disabled>
									</div>

									@if (!empty($element->is_required))
										<div class="col-lg-3">
											<label class="col-from-label">{{translate('Required')}}</label>
										</div>
										<div class="col-lg-7">															
												<select class="form-control aiz-selectpicker" name="required[]">
													<option value="false" {{ $element->is_required == 'false' ? "selected" : "" }}> Not Required</option>
                                                    <option value="true" {{ $element->is_required == 'true' ? "selected" : "" }}> Required </option>
												</select>
										</div>
									</div>
									@endif
								@endif
							@endforeach
						</div>
						<div class="col-lg-4">

							<ul class="list-group">
								<li class="list-group-item btn" style="text-align: left;" onclick="appenddToForm('text')">{{translate('Text Input')}}</li>
								<li class="list-group-item btn" style="text-align: left;" onclick="appenddToForm('select')">{{translate('Select')}}</li>
								<li class="list-group-item btn" style="text-align: left;" onclick="appenddToForm('multi-select')">{{translate('Multiple Select')}}</li>
								<li class="list-group-item btn" style="text-align: left;" onclick="appenddToForm('radio')">{{translate('Radio')}}</li>
								<li class="list-group-item btn" style="text-align: left;" onclick="appenddToForm('file')">{{translate('File')}}</li>
								@if ($is_location == false)
								<li class="list-group-item btn" id ="location-button" style="text-align: left;" onclick="appenddToForm('location')">{{translate('Location')}}</li>
								@endif
							</ul>

						</div>
					</div>
					<div class="form-group mb-0 text-right">
						<button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
					</div>
				</form>
			</div>
		</div>

	</div>

@endsection

@section('script')
	<script type="text/javascript">

		var i = 0;

		function add_customer_choice_options(em){
			var j = $(em).closest('.form-group.row').find('.option').val();
			var str = '<div class="form-group row">'
							+'<div class="col-sm-6 col-sm-offset-4">'
								+'<input class="form-control" type="text" name="options_'+j+'[]" value="" required>'
							+'</div>'
							+'<div class="col-sm-2"> <span class="btn btn-icon btn-circle icon-lg" onclick="delete_choice_clearfix(this)"><i class="las la-times"></i></span>'
							+'</div>'
						+'</div>'
			$(em).parent().find('.customer_choice_options_types_wrap_child').append(str);
		}
		function delete_choice_clearfix(em){
			$(em).parent().parent().remove();
		}
		function appenddToForm(type){
			//$('#form').removeClass('seller_form_border');
			if(type == 'text'){
				var str = '<div class="form-group row" style="background:rgb(227 227 227 / 25%);padding:10px 0;">'
								+'<input type="hidden" name="type[]" value="text">'
								+'<div class="col-lg-3">'
									+'<label class="col-from-label">Text</label>'
								+'</div>'
								+'<div class="col-lg-7">'
									+'<input class="form-control" type="text" name="label[]" placeholder="{{ translate('Label') }}">'
								+'</div>'
								+'<div class="col-lg-2">'
									+'<span class="btn btn-icon btn-circle icon-lg" onclick="delete_choice_clearfix(this)"><i class="las la-times"></i></span>'
								+'</div>'
								+'<div class="col-lg-3">'
									+'<label class="col-from-label">{{translate('Required')}}</label>'
								+'</div>'
								+'<div class="col-lg-7">'												
									+'<select class="form-control aiz-selectpicker" name="required[]">'
										+'<option value="false"> Not Required</option>'
                                        +'<option value="true"> Required </option>'
									+'</select>'
								+'</div>'
							+'</div>';
				$('#form').append(str);
			}
			else if (type == 'select') {
				i++;
				var str = '<div class="form-group row" style="background:rgb(227 227 227 / 25%);padding:10px 0;">'
								+'<input type="hidden" name="type[]" value="select"><input type="hidden" name="option[]" class="option" value="'+i+'">'
								+'<div class="col-lg-3">'
									+'<label class="col-from-label">Select</label>'
								+'</div>'
								+'<div class="col-lg-7">'
									+'<input class="form-control" type="text" name="label[]" placeholder="{{ translate('Select Label') }}" style="margin-bottom:10px">'
									+'<div class="customer_choice_options_types_wrap_child">'

									+'</div>'
									+'<button class="btn btn-success pull-right" type="button" onclick="add_customer_choice_options(this)"><i class="glyphicon glyphicon-plus"></i> Add option</button>'
								+'</div>'
								+'<div class="col-lg-2">'
									+'<span class="btn btn-icon btn-circle icon-lg" onclick="delete_choice_clearfix(this)"><i class="las la-times"></i></span>'
								+'</div>'
								+'<div class="col-lg-3">'
									+'<label class="col-from-label">{{translate('Required')}}</label>'
								+'</div>'
								+'<div class="col-lg-7">'												
									+'<select class="form-control aiz-selectpicker" name="required[]">'
										+'<option value="false"> Not Required</option>'
                                        +'<option value="true"> Required </option>'
									+'</select>'
								+'</div>'
							+'</div>';
				$('#form').append(str);
			}
			else if (type == 'multi-select') {
				i++;
				var str = '<div class="form-group row" style="background:rgb(227 227 227 / 25%);padding:10px 0;">'
								+'<input type="hidden" name="type[]" value="multi_select"><input type="hidden" name="option[]" class="option" value="'+i+'">'
								+'<div class="col-lg-3">'
									+'<label class="col-from-label">Multiple select</label>'
								+'</div>'
								+'<div class="col-lg-7">'
									+'<input class="form-control" type="text" name="label[]" placeholder="{{ translate('Multiple Select Label') }}" style="margin-bottom:10px">'
									+'<div class="customer_choice_options_types_wrap_child">'

									+'</div>'
									+'<button class="btn btn-success pull-right" type="button" onclick="add_customer_choice_options(this)"><i class="glyphicon glyphicon-plus"></i> Add option</button>'
								+'</div>'
								+'<div class="col-lg-2">'
									+'<span class="btn btn-icon btn-circle icon-lg" onclick="delete_choice_clearfix(this)"><i class="las la-times"></i></span>'
								+'</div>'
								+'<div class="col-lg-3">'
									+'<label class="col-from-label">{{translate('Required')}}</label>'
								+'</div>'
								+'<div class="col-lg-7">'												
									+'<select class="form-control aiz-selectpicker" name="required[]">'
										+'<option value="false"> Not Required</option>'
                                        +'<option value="true"> Required </option>'
									+'</select>'
								+'</div>'
							+'</div>';
				$('#form').append(str);
			}
			else if (type == 'radio') {
				i++;
				var str = '<div class="form-group row" style="background:rgb(227 227 227 / 25%);padding:10px 0;">'
								+'<input type="hidden" name="type[]" value="radio"><input type="hidden" name="option[]" class="option" value="'+i+'">'
								+'<div class="col-lg-3">'
									+'<label class="col-from-label">Radio</label>'
								+'</div>'
								+'<div class="col-lg-7">'
									+'<div class="customer_choice_options_types_wrap_child">'
									+'<input class="form-control" type="text" name="label[]" placeholder="{{ translate('Radio Label') }}" style="margin-bottom:10px">'

									+'</div>'
									+'<button class="btn btn-success pull-right" type="button" onclick="add_customer_choice_options(this)"><i class="glyphicon glyphicon-plus"></i> Add option</button>'
								+'</div>'
								+'<div class="col-lg-2">'
									+'<span class="btn btn-icon btn-circle icon-lg" onclick="delete_choice_clearfix(this)"><i class="las la-times"></i></span>'
								+'</div>'
								+'<div class="col-lg-3">'
									+'<label class="col-from-label">{{translate('Required')}}</label>'
								+'</div>'
								+'<div class="col-lg-7">'												
									+'<select class="form-control aiz-selectpicker" name="required[]">'
										+'<option value="false"> Not Required</option>'
                                        +'<option value="true"> Required </option>'
									+'</select>'
								+'</div>'
							+'</div>';
				$('#form').append(str);
			}
			else if (type == 'file') {
				var str = '<div class="form-group row" style="background:rgb(227 227 227 / 25%);padding:10px 0;">'
								+'<input type="hidden" name="type[]" value="file">'
								+'<div class="col-lg-3">'
									+'<label class="col-from-label">File</label>'
								+'</div>'
								+'<div class="col-lg-7">'
									+'<input class="form-control" type="text" name="label[]" placeholder="{{ translate('Label') }}">'
								+'</div>'
								+'<div class="col-lg-2">'
									+'<span class="btn btn-icon btn-circle icon-lg" onclick="delete_choice_clearfix(this)"><i class="las la-times"></i></span>'
								+'</div>'
								+'<div class="col-lg-3">'
									+'<label class="col-from-label">{{translate('Required')}}</label>'
								+'</div>'
								+'<div class="col-lg-7">'												
									+'<select class="form-control aiz-selectpicker" name="required[]">'
										+'<option value="false"> Not Required</option>'
                                        +'<option value="true"> Required </option>'
									+'</select>'
								+'</div>'
							+'</div>';
				$('#form').append(str);
			}
			else if(type == 'location'){
				var str = '<div class="form-group row" style="background:rgb(227 227 227 / 25%);padding:10px 0;">'
								+'<input type="hidden" name="type[]" value="location">'
								+'<input type="hidden" name="label[]" value="location">'
								+'<div class="col-lg-3">'
									+'<label class="col-from-label">Location</label>'
								+'</div>'
								
								+'<div class="col-lg-7">'
									+'<input class="form-control" type="text" name="options_location[]" value="Provinsi" disabled>'
								+'</div>'
								+'<div class="col-lg-2">'
									+'<span class="btn btn-icon btn-circle icon-lg" onclick="delete_choice_clearfix(this)"><i class="las la-times"></i></span>'
								+'</div>'

								+'<div class="col-lg-3">'
									+'<label class="col-from-label"></label>'
								+'</div>'
								+'<div class="col-lg-7">'
									+'<input class="form-control" type="text" name="options_location[]" value="Kota" disabled>'
								+'</div>'

								+'<div class="col-lg-3">'
									+'<label class="col-from-label"></label>'
								+'</div>'
								+'<div class="col-lg-7">'
									+'<input class="form-control" type="text" name="options_location[]" value="Kecamatan" disabled>'
								+'</div>'

								+'<div class="col-lg-3">'
									+'<label class="col-from-label"></label>'
								+'</div>'
								+'<div class="col-lg-7">'
									+'<input class="form-control" type="text" name="options_location[]" value="Kelurahan" disabled>'
								+'</div>'

								+'<div class="col-lg-3">'
									+'<label class="col-from-label"></label>'
								+'</div>'
								+'<div class="col-lg-7">'
									+'<input class="form-control" type="text" name="options_location[]" value="Kode Pos" disabled>'
								+'</div>'

								+'<div class="col-lg-3">'
									+'<label class="col-from-label">{{translate('Required')}}</label>'
								+'</div>'
								+'<div class="col-lg-7">'												
									+'<select class="form-control aiz-selectpicker" name="required[]">'
										+'<option value="false"> Not Required</option>'
                                        +'<option value="true"> Required </option>'
									+'</select>'
								+'</div>'
								
							+'</div>';
				$('#form').append(str);
			}
		}

		function disabledButtonAddLocation(){
			document.getElementById('location-button').onclick = null;
		}
	</script>
@endsection
