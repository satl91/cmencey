@layout('layouts.admin')
@section('content')
<div class="container-fluid">

	<div class="row-fluid">

		<div class="span10 offset1">

			<div class="row-fluid span6 offset3">
				
				<h4 class="text-center">Editar documento</h4>
				<div class="space1"></div>

			</div>

			<div class="row-fluid">
				
				{{ Form::open('admin/docs/edit/'.$document_type->id,'POST', array('class' => 'form-horizontal')) }}

				<div class="span6 offset3">
					
					<div class="white-area white-area-content">
						
						<div class="control-group">

							{{ $errors->first('document_type_description','<p style="color: red; text-align:center; margin-left:55px; margin-bottom:1px;">:message</p>') }}	
							<label class="control-label">Nombre</label>

							<div class="controls">

								<input id="document_type_description" name="document_type_description" type="text" value="{{$document_type->description}}" class="input" required="">

							</div>

						</div>

						<div class="control-group">

							{{ $errors->first('document_type_expires','<p style="color: red; text-align:center; margin-left:55px; margin-bottom:1px;">:message</p>') }}	
							<label class="control-label">¿Llevar control del vencimiento?</label>

							<div class="controls">	

								@if ($document_type->expires == 1)

									<label class="radio">

										<input type="radio" name="document_type_expires" id="document_type_expires" value="1" required checked> Si 

									</label>

									<label class="radio">

										<input type="radio" name="document_type_expires" id="document_type_expires" value="0" required> No

									</label>

								@else

									<label class="radio">

										<input type="radio" name="document_type_expires" id="document_type_expires" value="1" required> Si 

									</label>

									<label class="radio">

										<input type="radio" name="document_type_expires" id="document_type_expires" value="0" required checked> No

									</label>

								@endif

							</div>

						</div>

					</div>	

				</div>

			</div>

			<div class="row-fluid">
			
				<div class="span2 offset5">
					
					<div class="control-group">

						<div class="controls">
						
							<div class="space1"></div>
							
							<button id="submit" name="submit" class="btn btn-primary btn-block"><i class="icon-ok icon-white"></i> Guardar</button>
					
						</div>

					</div>
				
				</div>
			
			</div>

			{{ Form::close() }}

		</div>

	</div>

</div>

	
	</div>

</div>
@endsection