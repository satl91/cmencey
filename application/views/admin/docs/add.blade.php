<div class="container-fluid">

	<div class="row-fluid">

		<div class="span9">

			<div class="row-fluid">

				<legend>Añadir nuevo documento</legend>

			</div>

			<div class="row-fluid">

				{{ Form::open('admin/docs/add','POST', array('class' => 'form-horizontal')) }}

				<div class="offset3">

					<div class="control-group">

						<label class="control-label">Nombre</label>

						<div class="controls">

							<input id="document_type_description" name="document_type_description" type="text" placeholder="Ej: Cédula de identidad" class="input" required="">

						</div>

					</div>

					<div class="control-group">

						<label class="control-label">¿Llevar control del vencimiento?</label>

						<div class="controls">	

							<label class="radio">

								<input type="radio" name="document_type_expires" id="document_type_expires" value="1" required> Si 

							</label>

							<label class="radio">

								<input type="radio" name="document_type_expires" id="document_type_expires" value="0" required> No

							</label>

						</div>

					</div>

				</div>	

			</div>

			<div class="row-fluid">

				<div class="span2 offset5">

					<div class="control-group">

						<div class="controls">

							<button id="submit" name="submit" class="btn btn-primary btn-block"><i class="icon-ok icon-white"></i> Añadir</button>

						</div>

					</div>

				</div>

			</div>

			{{ Form::close() }}

		</div>

		<div class="span3">

			<legend>Ayuda</legend>
			Informacion necesaria

		</div>

	</div>

</div>

	
	</div>

</div>