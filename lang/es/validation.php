<?php
return [
 'required'=>'El campo :attribute es obligatorio.', 'email'=>'El campo :attribute debe ser un correo electrónico válido.',
 'unique'=>'El valor de :attribute ya está registrado.', 'confirmed'=>'La confirmación de :attribute no coincide.',
 'min'=>['string'=>'El campo :attribute debe tener al menos :min caracteres.','numeric'=>'El campo :attribute debe ser al menos :min.'],
 'max'=>['string'=>'El campo :attribute no debe superar :max caracteres.','numeric'=>'El campo :attribute no debe superar :max.'],
 'date'=>'El campo :attribute debe ser una fecha válida.', 'after'=>'El campo :attribute debe ser posterior a :date.',
 'after_or_equal'=>'El campo :attribute debe ser una fecha posterior o igual a :date.', 'in'=>'El valor seleccionado para :attribute no es válido.',
 'numeric'=>'El campo :attribute debe ser numérico.', 'integer'=>'El campo :attribute debe ser un número entero.',
 'array'=>'El campo :attribute debe ser una lista válida.',
 'attributes'=>['email'=>'correo electrónico','password'=>'contraseña','telefono'=>'teléfono','name'=>'nombre','inicio'=>'fecha y hora','fecha'=>'fecha','servicios'=>'servicios'],
];
