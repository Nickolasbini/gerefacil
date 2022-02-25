<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'license_number',
        'master_admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function getCategories()
    {
        //return $this->hasMany('App\Models\Category', 'id_user');
    }

    const SEX_VALUES = ['M','F','U'];

    /**
     * Method which validates the informed CPF format
     * @version 1.0 - 20210406
     * @param  <string> the CPF informed
     * @return <bool> 
     */
	public function validateCPF($cpf)
	{
	    $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
	    if (strlen($cpf) != 11) {
	        return false;
	    }
	    if (preg_match('/(\d)\1{10}/', $cpf)) {
	        return false;
	    }
	    for ($t = 9; $t < 11; $t++) {
	        for ($d = 0, $c = 0; $c < $t; $c++) {
	            $d += $cpf[$c] * (($t + 1) - $c);
	        }
	        $d = ((10 * $d) % 11) % 10;
	        if ($cpf[$c] != $d) {
	            return false;
	        }
	    }
	    return true;
	}

    /**
     * Tries to get data from Via Cep oficial website
     * @version 1.0 - 20210406
     * @param  <string> the CEP informed
     * @return @return  <array> keys 'streetName' | 'neighborhood' |'state' or <bool> on failure
     */
    public function getCEPData($cep)
    {
        // using another web site
    	$url = 'https://viacep.com.br/ws/'.$cep.'/json/';
    	$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $answer = curl_exec($ch);
        $answerArray  =json_decode($answer, true);
        if(curl_errno($ch) || is_null($answerArray)){
        	return null;
        }
        if(!array_key_exists('cep', $answerArray))
        	return null;
        $response = [
        	'cep' 		   => $answerArray['cep'],
        	'streetName'   => $answerArray['logradouro'],
        	'neighborhood' => $answerArray['bairro'],
        	'cityName'     => $answerArray['localidade'],
        	'stateCode'    => $answerArray['uf']
        ];
		return $response;
    }
}
