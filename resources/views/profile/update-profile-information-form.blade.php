<x-jet-form-section submit="updateProfileInformation">
    <x-slot name="title">
        <?= ucfirst(translate('profile information')) ?>
    </x-slot>

    <x-slot name="description">
        <?= ucfirst(translate('update your profile information')) ?>
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input type="file" class="hidden"
                            wire:model="photo"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-jet-label for="photo" value="{{ __('Photo') }}" />

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{URL::asset('/images/account-avatar.svg')}}" alt="{{ $this->user->name }}" class="rounded-full h-20 w-20 object-cover profile-photo" style="display:none;">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <x-jet-secondary-button class="mt-2 mr-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    <?= ucfirst(translate('select a new photo')) ?>
                </x-jet-secondary-button>

                @if ($this->user->profile_photo_path)
                    <x-jet-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        <?= ucfirst(translate('remove photo')) ?>
                    </x-jet-secondary-button>
                @endif

                <x-jet-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="name" value="{{ __('Name') }}" />
            <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="state.name" autocomplete="name" />
            <x-jet-input-error for="name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="email" value="{{ __('Email') }}" />
            <x-jet-input id="email" type="email" class="mt-1 block w-full" wire:model.defer="state.email" />
            <x-jet-input-error for="email" class="mt-2" />
        </div>

        @if(!Auth::user()->is_admin)
            <!-- CPF -->
            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="cpf" value="CPF" />
                <x-jet-input id="cpf" type="cpf" class="mt-1 block w-full" wire:model.defer="state.cpf" />
                <x-jet-input-error for="cpf" class="mt-2" />
            </div>

            <!-- CEP -->
            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="cep" value="CEP" />
                <x-jet-input id="cep" type="cep" class="mt-1 block w-full" wire:model.defer="state.cep" />
                <x-jet-input-error for="cep" class="mt-2" />
            </div>

            <!-- ADDRESS -->
            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="address" value="address" />
                <x-jet-input id="address" type="address" class="mt-1 block w-full" wire:model.defer="state.address" />
                <x-jet-input-error for="address" class="mt-2" />
            </div>

            <!-- DATE OF BIRTH -->
            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="dateOfBirth" value="dateOfBirth" />
                <x-jet-input id="dateOfBirth" type="date" class="mt-1 block w-full" wire:model.defer="state.dateOfBirth" />
                <x-jet-input-error for="dateOfBirth" class="mt-2" />
            </div>
        @endif
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            <?= ucfirst(translate('saved')) . '.' ?>
        </x-jet-action-message>

        <x-jet-button wire:loading.attr="disabled" wire:target="photo" id="save-profile-data">
            <?= ucfirst(translate('save')) ?>
        </x-jet-button>
    </x-slot>
</x-jet-form-section>
<script>
    // CPF
    var typingTimer;                //timer identifier
    var doneTypingInterval = 2000;  //time in ms, 2 seconds for example
    var $input = $('#cpf');
    $input.on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(checkCPF, doneTypingInterval);
    });
    //on keydown, clear the countdown 
    $input.on('keydown', function () {
    clearTimeout(typingTimer);
    });
    //user is "finished typing," do something
    function checkCPF() {
        var cpf = $('#cpf').val();
        $.ajax({
            url: "<?= env('APP_URL') ?>" + "/user/validatecpf",
            method: 'POST',
            data: {cpf: cpf},
            dataType: "JSON",
            success: function(result){
                if(result.isValid == false){
                    alert('invalid cpf');
                }
            }
        });
    }

    // CEP
    var typingTimer;                //timer identifier
    var doneTypingInterval = 2000;  //time in ms, 2 seconds for example
    var $input = $('#cep');
    $input.on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(checkCEP, doneTypingInterval);
    });
    //on keydown, clear the countdown 
    $input.on('keydown', function () {
    clearTimeout(typingTimer);
    });
    //user is "finished typing," do something
    function checkCEP() {
        var cep = $('#cep').val();
        $.ajax({
            url: "<?= env('APP_URL') ?>" + "/user/getcepdata",
            method: 'POST',
            data: {cep: cep},
            dataType: "JSON",
            success: function(result){
                if(result.success == true && result.hasResult == true){
                    var content = result.content;
                    var address = content.streetName + ' ' + content.neighborhood + ' ' + content.cityName + ' ' + content.stateCode;
                    $('#address').val(address);
                    $('#cep').val(content.cep);
                }
            }
        });
    }
</script>