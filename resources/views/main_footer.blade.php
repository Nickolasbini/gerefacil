<?php $data = \App\Helpers\Functions::adminRelatedDataArray(); ?>
<?php $bgColor = (isset($bgColor) ? $bgColor : 'light'); ?>
@if($bgColor == 'light')
    <footer id="footer" class="container-fluid pt-5 pb-5 mt-5" style="background-color:rgba(184, 184, 184, 0.14)">
@elseif($bgColor == 'dark')
    <footer id="footer" class="container-fluid pt-5 pb-5 mt-5" style="background-color:rgba(17, 17, 17, 0.25)">
@endif
    <div class="row col-sm-10 col-md-12 m-auto d-flex justify-content-around">
        <div class="col-sm-10 col-md-3 mt-4-sm">
            <h2 class="primary-color h4">
                SOBRE
            </h2>
            <p class="text-left">
                <p>GereFacil o seu sistema de gerenciamento e vendas online.</p>
                <p>
                    Com o GereFacil é tudo mais fácil!
                </p>
            </p>
        </div>
        <div class="col-sm-10 col-md-3 mt-4-sm">
            <h2 class="primary-color h4 text-center">
                NOS SIGA
            </h2>
            <div class="d-flex flex-column text-center">
                @if($data['whatsApp'] != '')
                    <a class="m-auto" href="https://api.whatsapp.com/send?text={{$data['whatsMessage']}}&phone={{$data['whatsApp']}}" target="_blank">
                        <img class="responsible-icon mt-2 mb-2 cursor-pointer color-social-icon" src="{{ asset('images/whatsapp-gray.svg') }}" data-name="whatsapp" data-toggle="tooltip" data-placement="top" title="Abrir whatsApp">
                    </a>
                @endif
                @if($data['facebook'] != '')
                    <a class="m-auto" href="{{$data['facebook']}}" target="_blank">
                        <img class="responsible-icon mt-2 mb-2 cursor-pointer color-social-icon" src="{{ asset('images/facebook-gray.svg') }}" data-name="facebook" data-toggle="tooltip" data-placement="top" title="Ver página do Facebook">
                    </a>
                @endif
                @if($data['instagram'] != '')
                    <a class="m-auto" href="{{$data['instagram']}}" target="_blank">
                        <img class="responsible-icon mt-2 mb-2 cursor-pointer color-social-icon" src="{{ asset('images/instagram-gray.svg') }}" data-name="instagram" data-toggle="tooltip" data-placement="top" title="Ver perfil do instagram">
                    </a>
                @endif
            </div>
        </div>
        <div class="col-sm-10 col-md-3 mt-4-sm">
            <h2 class="primary-color h4">
                CONTATO
            </h2>
            <div>
                <em class="lead SendEmail cursor-pointer" data-toggle="tooltip" data-placement="top" title="Enviar email">
                    Por email
                </em>
            </div>
        </div>
    </div>
</footer>

<section id="company-data" class="container text-center p-4">
    <p class="">
        <a class="opacity-hover" href="{{ \App\Helpers\Functions::viewLink('termsofservice') }}">Termos de serviço</a>
        |
        <a class="opacity-hover" href="{{ \App\Helpers\Functions::viewLink('privacypolicy') }}">Política de privacidade</a>
        |
        <a class="opacity-hover" href="{{ \App\Helpers\Functions::viewLink('cookiespolicy') }}">Política de cookies</a>
    </p>
    <p class="h6 cursor-pointer opacity-hover">
        <a target="_blank" href="https://www.cervodigital.com.br" style="text-decoration:none;color:var(--bs-body-color)">
            © Copyright agência cervo digital. 2021 - Todos os direitos reservados
        </a>
    </p>
</section>

<script>
    var screenWidht = $(window).width();
    function checkFooterIcons(){
		if(screenWidht < 760){
			$('.color-social-icon').off('mouseenter');
			$('.color-social-icon').off('mouseleave');
			$('footer').css('text-align', 'center');
			var socialMediaIcons = $('.color-social-icon');
			socialMediaIcons.each(function(){
				var name = $(this).attr('data-name');
				$(this).attr('src', '/images/'+ name + '.svg');
			});
		}
	}
	checkFooterIcons();

    $('.SendEmail').click(function (event) {
        var email = "{{$data['email']}}";
        var subject = 'Quero tirar uma dúvida';
        var emailBody = 'Olá ...';
        document.location = "mailto:"+email+"?subject="+subject+"&body="+emailBody;
    });
</script>