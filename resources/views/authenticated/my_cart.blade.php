@include('master_head')
@include('header-menu', ['enableSearch' => \App\Helpers\Functions::viewLink('/')])

<section class="container mt-5 mb-5 col-sm-10 col-md-8">
    <div>
        <p class="h4 mt-5 mb-5">
            {{ ucfirst(translate('my cart')) }}
        </p>
    </div>
    <section id="cart-list" class="row">
        <div class="col-md-7">
            @foreach($productOrder as $orderedProduct)
                <div class="row border-t pt-3 mb-4">
                    <?php $orderedProduct->addProductObject() ?>
                    <div class="d-flex">
                        <div class="col-sm-10 col-md-4">
                            <img src="{{ $orderedProduct->product->getPhotoAsBase64() }}" alt="product photo" class="img-fluid rounded p-3">
                        </div>
                        <div class="col-sm-10 col-md-8 p-3 d-flex flex-column">
                            <div class="d-flex justify-content-between mt-auto mb-auto">
                                <label class="">
                                    {{ $orderedProduct->product->name }}
                                </label>
                                <a href="">X</a>
                            </div>
                            <div class="d-flex justify-content-between mt-auto mb-auto">
                                <div class="d-flex">
                                    <a href="" class="btn btn-light m-2 ps-2 pe-2">
                                        -
                                    </a>
                                    <a class="btn btn-light m-2 ps-2 pe-2">
                                        {{ $orderedProduct->quantity }}
                                    </a>
                                    <a href="" class="btn btn-light m-2 ps-2 pe-2">
                                        +
                                    </a>
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="text-right">
                                        <small>{{ \App\Helpers\Functions::formatMoney($orderedProduct->product->price); }} |un</small>
                                    </div>
                                    <div class="text-right">
                                        {{ \App\Helpers\Functions::formatMoney($orderedProduct->totalSum); }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-md-5">
            <div class="p-3 border shadow rounded">
                <div id="sub-total" class="d-flex">
                    <label>
                        Subtotal
                    </label>
                    <label>
                        {{ \App\Helpers\Functions::formatMoney($order->getSubTotal()); }}
                    </label>
                </div>
                <p>
                    Calcular frete
                </p>
                <div id="cep-calculator" class="d-flex">
                    <input id="cep-value" class="border w-100" style="border-top-left-radius: 5px; border-bottom-left-radius: 5px;" type="text">
                    <a class="p-2 btn btn-dark" style="border-top-left-radius: unset; border-bottom-left-radius: unset; border-top-right-radius: 5px; border-bottom-right-radius: 5px;">
                        Icone
                    </a>
                </div>
                <div id="cep-options">

                </div>
                <div id="total-price" class="d-flex">

                </div>
                <div class="mt-5 mb-2">
                    <a href="" class="btn btn-success w-100">iconeSacola Finalizar Compra</a>
                </div>
            </div>
            <div class="m-2">
                <div id="descount-button">
                    <a>BTN adicionar cupom de desconto</a>
                </div>
                <div id="descount-input" class="d-flex">
                    <input class="border" style="border-top-left-radius: 5px; border-bottom-left-radius: 5px;" type="text" placeholder="insira o cupom">
                    <a class="p-2 btn btn-dark" style="border-top-left-radius: unset; border-bottom-left-radius: unset; border-top-right-radius: 5px; border-bottom-right-radius: 5px;">
                        Adicionar
                    </a>
                </div>
            </div>
        </div>
    </section>
    <div class="">
        <a href="{{\App\Helpers\Functions::viewLink('/')}}">
            Voltar
        </a>
    </div>
</section>

@include('main_footer')

<script>
    // allows only numbers and request CEP when reached max number (8)
    var formerCEP = '';

    $('#cep-value').on('keypress', function(){
        var enteredCep = $(this).val();
        formatedCep = onlyNumbers(enteredCep);

        if(formatedCep.length > 8){
            checkCEP();
        }else{
            formerCEP = formatedCep;
        }
        $(this).val(formerCEP);
    });

    function onlyNumbers(value){
        return value.replace(/\D/g, "");
    }

    function checkCEP(){
        alert('yes');
    }
</script>