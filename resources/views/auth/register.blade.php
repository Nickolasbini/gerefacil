<link rel="stylesheet" href="{{url('/externalfeatures/bootstrap.css')}}">
<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-jet-label for="name" value="{{ __('Name') }}" />
                <x-jet-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-jet-label for="email" value="{{ __('Email') }}" />
                <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>

            <div class="mt-4">
                <x-jet-label for="password" value="{{ __('Password') }}" />
                <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-jet-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-jet-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="mt-4 hide-until-email-is-informed checkbox-of-licence">
                <label><?= ucfirst(translate("i have a licence number")); ?></label>
                <input id="is_admin" type="checkbox" name="is_admin" value="0" onclick="showLicenceField()">
            </div>

            <div class="mt-4 hide-until-email-is-informed activeted-licence" style="display:none;">
                <label><?= ucfirst(translate("my license")); ?>:</label>
            </div>

            <div id="serialNumberDiv" class="mt-4 hide-until-email-is-informed" style="display:none;">
                <x-jet-label for="serial" value="serial number" />
                <x-jet-input id="serial" class="block mt-1 w-full" type="text" name="serial" value="000000000" autocomplete="new-serial" />
                <div class="btn btn-dark p-2 mt-2 rounded" id="validate-serial">
                    <?= translate('validate') ?>
                </div>
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-jet-label for="terms">
                        <div class="flex items-center">
                            <x-jet-checkbox name="terms" id="terms"/>

                            <div class="ml-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-jet-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-jet-button class="ml-4" id="register-button">
                    {{ __('Register') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>
<script src="{{url('/externalfeatures/jquery.js')}}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });
    $('#email').on('input', function(){
        $('.hide-until-email-is-informed').removeClass('hide-until-email-is-informed');
    });

    document.addEventListener("DOMContentLoaded", function(event) {
        document.getElementById('is_admin').checked = false;
        showLicenceField();
    });

    function showLicenceField(){
        var isSelected = document.getElementById('is_admin').checked;
        if(isSelected == true){
            document.getElementById('serialNumberDiv').style = '';
            document.getElementById('serial').value = '';
            document.getElementById('register-button').style = 'pointer-events:none';
        }else{
            document.getElementById('serialNumberDiv').style = 'display:none;';
            document.getElementById('serial').value = '000000000';
            document.getElementById('register-button').style = 'pointer-events:unset';
        }
    }

    $('#validate-serial').on('click', function(){
        var serial = $('#serial').val();
        var email = $('#email').val();
        if(email == ''){
            alert('you must inform an email to proceed');
            return;
        }
        if(serial == ''){
            alert('you must inform a serial to proceed');
            return;
        }
        $.ajax({
            url: "<?= env('APP_URL') ?>" + "/user/checkserial",
            method: 'POST',
            data: {serial: serial, email: email},
            dataType: "JSON",
            success: function(result){
                if(result.isValid == false){
                    alert('invalid serial');
                    return;
                }else{
                    alert('you are now using this serial');
                    document.getElementById('register-button').style = 'pointer-events:unset';
                    $('#validate-serial').remove();
                    $('#serial').css('pointer-events', 'none');
                    $('#serialNumberDiv').hide();
                    $('.checkbox-of-licence').remove();
                    $('.activeted-licence').append(': ' + $('#serial').val());
                    $('.activeted-licence').show();
                }
            }
        });
    });
</script>
<style>
    .hide-until-email-is-informed{
        display:none;
    }
</style>