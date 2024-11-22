<template>
    <div class="page-center">
        <div class="page-center-in">
            <div class="container-fluid">
                <form class="sign-box" id="form_inicio" name="form_inicio" role="form">

                    <div class="sign-avatar">
                        <img src="/vendor/gyomanager/img/avatar-sign.png" alt="">
                    </div>
                    <header class="sign-title"><b>Iniciar Sesión</b></header>

                    <div class="form-group has-feedback" :class="{'has-error': errorsEmail}">
                        <div class="input-group">
                            <input v-model="loginDetails.email" required class="form-control" type="email" id="email" name="email" placeholder="E-mail" minlength="4" />
                            <span style="float: right;position: absolute;top:7px;" class="glyphicon glyphicon-remove form-control-feedback"></span>
                            <span v-if="errorsEmail" class="help-block">
                                <strong>{{emailError}}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="form-group has-feedback" :class="{'has-error': errorsPassword}">
                        <div class="input-group">
                            <input v-model="loginDetails.password" required type="password" id="password" name="password" class="form-control" placeholder="Contraseña" minlength="4"/>

                            <span style="float: right;position: absolute;top:7px;" class="glyphicon glyphicon-remove form-control-feedback"></span>
                            <span v-if="errorsPassword" class="help-block">
                                <strong>{{passwordError}}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox float-left">
                            <input type="checkbox" id="signed-in"/>
                            <label for="signed-in"><b>Recuérdame</b></label>
                        </div>
                        <div class="float-right reset">
                            <a href="recuperar.php"><b>¿Olvidó su contraseña?</b></a>
                        </div>
                    </div>

                    <button @click.prevent="loginPost" style="position: relative;top: 10px;" type="submit" class="btn btn-rounded btn-success">
                        <i class="fa fa-unlock-alt" style="position: relative;top: 1px;"></i>
                        <b>Acceder</b>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                loginDetails: {
                    email: '',
                    password: '',
                    remember: true
                },
                errorsEmail: false,
                errorsPassword: false,
                emailError: null,
                passwordError: null
            }
        },
        methods:{
            loginPost() {
                let vm = this;
                axios.post('/login', vm.loginDetails)
                    .then(function(response) {
                        location.href = "/home";
                        console.log(response);
                    })
                    .catch(function(error) {
                        var errors = error.response;
                        console.log(errors);
                        if(errors.statusText === 'Unprocessable Entity' || errors.status === 442) {
                            console.log(errors.data);
                            if(errors.data) {
                                if(errors.data.errors.email) {
                                    vm.errorsEmail = true;
                                    vm.emailError = _.isArray(errors.data.errors.email) ? errors.data.errors.email[0]: errors.data.errors.email
                                } else {
                                    vm.errorsEmail = false;
                                    vm.emailError = null;
                                }

                                if(errors.data.errors.password) {
                                    vm.errorsPassword = true;
                                    vm.passwordError = _.isArray(errors.data.errors.password) ? errors.data.errors.password[0]: errors.data.errors.password
                                } else {
                                    vm.errorsPassword = false;
                                    vm.passwordError = null;
                                }
                            }
                        }

                    });
            }
        },
        name: "login-component"
    }
</script>