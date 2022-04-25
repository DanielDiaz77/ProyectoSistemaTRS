@extends('auth.contenido')

@section('login')
<div class="row justify-content-center mt-5">
    <div class="col-md-8">
        <div class="card-group mb-0">
            <div class="card p-4">
                <form class="form-horizontal" method="POST" action="{{ route('login')}}">
                    {{ csrf_field() }}
                    <div class="card-body">
                        <img src="img/logo.png" class="img-responsive" alt="Troystone" />
                        <h1>Acceder</h1>
                        <p class="text-muted">Control de acceso al sistema</p>
                        <div class="form-group">
                            <i class="fa fa-user mr-2" aria-hidden="true"></i><label for="usuario" ><Strong>Usuario</Strong></label>
                            <input type="text" class="form-control {{ $errors->has('usuario') ? 'is-invalid' : '' }}"
                                name="usuario" placeholder="Ingresa el nombre del usuario" value="{{ old('usuario') }}">
                            {!! $errors->first('usuario', '<span class="text-danger font-weight-bold">:message</span>') !!}
                        </div>
                        <div class="form-group">
                            <i class="fa fa-lock mr-2" aria-hidden="true"></i><label for="password" ><Strong>Contraseña</Strong></label>
                            <input type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }} "
                                name="password" placeholder="Ingresa la contraseña">
                            {!! $errors->first('password', '<span class="text-danger font-weight-bold">:message</span>') !!}
                        </div>
                        <button type="submit" class="btn troybackg-light btn-block">Acceder</button>
                    </div>
                </form>
            </div>
            <div class="card text-white troybackg py-5 d-none d-md-none d-lg-block" style="width:44%">
                <div class="card-body text-center">
                    <div>
                        <h2 class="text-center">Inventarios TroyStone&reg;</h2>
                        <p>El acceso es autorizado y gestionado por la empresa, en caso de requerirlo, comunicarse a sistemas@troystone.com.mx .</p>
                        <a href="https://www.troystone.com.mx" target="_blank" class="btn btn-light active mt-3">Visitanos!</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
