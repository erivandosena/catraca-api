<?php
/**
 * @author Jefferson Uchoa Ponte
 * @version 1.0
 * @copyright UNILAB - Universidade da Integracao Internacional da Lusofonia Afro-Brasileira.
 * @package Util
 */

   /*
    * Legenda para tipos de Campos:
    *   MAIL = 1,
    *   PASS = 2,
    *   TEXT = 3,
    *   ENDERECO = 4,
    *   NUMERICO = 5,
    *   MATRICULA = 6
    *   CPF = 7
    *   Data = 8
    *   MAC = 9
    *   cep = 10
    *   valida_fone = 11
    *   login = 12
   */   

class Validacoes{
    
	/**
	 * 
	 * @var unknown
	 */
    private $expressao;
    
    /**
     * 
     * @var unknown
     */
    private $tipoCampo =  array(1,2,3,4,5,6,7,8,9,10,11,12);

	/**
	 * 
	 * @param int $tipo_campo
	 * @param string $valor
	 */
    public function validaCampos($tipo_campo, $valor){

        if(!in_array($tipo_campo,$this->tipoCampo)){

            echo "<pre style='color:#f00;'>Tipo de campo invÃƒÂ¡lido.</pre>";

        }else{

            switch ($tipo_campo) {
                case 1:
                   return $this->valida_mail($valor);
                    break;
                case 2:
                    return $this->valida_senha($valor);
                    break;
                case 3:
                    return $this->valida_text($valor);
                    break;
                case 4:
                    return $this->valida_endereco($valor);
                    break;
                
                case 6:
                    return $this->valida_matricula($valor);
                    break;
                
                case 7:
                    return $this->valida_CPF($valor);
                    break;
                
                case 8:
                    return $this->valida_data($valor);
                    break;
                case 9:
                    return $this->valida_mac($valor);
                    break;
                case 10:
                    return $this->valida_cep($valor);
                    break;
                case 11:
                    return $this->valida_fone($valor);
                    break;
                case 12:
                    return $this->valida_login($valor);
                    break;
                default:
                    echo "Não foi possivel realizar a validação do campo ".$tipo_campo;
                    break;
            }
        }
    }

	/**
	 * 
	 * @param string $matricula
	 * @param int $tam_max
	 */
    private function valida_matricula($matricula,$tam_max = 11){
       
        if(empty($matricula)){
            return FALSE;
        }else if (!is_numeric($matricula)) {
            return FALSE;
        }else if (strlen($matricula) > $tam_max) {
            return FALSE;
        }else{
            return TRUE;
        }

    }

	/**
	 * 
	 * @param string $email
	 */
    private function valida_mail($email){

        if($email == ""){
            return FALSE;
        }
        else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return FALSE;
        }
        else{
            return TRUE;
        }
    }
    
    /**
     * 
     * @param string $strText
     * @return boolean
     */
    private function valida_text($strText){
        
        $erro=FALSE;
        $conversao = array('ÃƒÂ¡' => 'a','ÃƒÂ ' => 'a','ÃƒÂ£' => 'a','ÃƒÂ¢' => 'a', 'ÃƒÂ©' => 'e',
         'ÃƒÂª' => 'e', 'ÃƒÂ­' => 'i', 'ÃƒÂ¯'=>'i', 'ÃƒÂ³' => 'o', 'ÃƒÂ´' => 'o', 'ÃƒÂµ' => 'o', "ÃƒÂ¶"=>"o",
         'ÃƒÂº' => 'u', 'ÃƒÂ¼' => 'u', 'ÃƒÂ§' => 'c', 'ÃƒÂ±'=>'n', 'Ãƒï¿½' => 'A', 'Ãƒâ‚¬' => 'A', 'ÃƒÆ’' => 'A',
         'Ãƒâ€š' => 'A', 'Ãƒâ€°' => 'E', 'ÃƒÅ ' => 'E', 'Ãƒï¿½' => 'I', 'Ãƒï¿½'=>'I', "Ãƒâ€“"=>"O", 'Ãƒâ€œ' => 'O',
         'Ãƒâ€�' => 'O', 'Ãƒâ€¢' => 'O', 'ÃƒÅ¡' => 'U', 'ÃƒÅ“' => 'U', 'Ãƒâ€¡' =>'C', 'Ãƒâ€˜'=>'N');
        
        $text=(trim($strText));
        $text=str_replace(" ","",$text);
        $text = strtr($text, $conversao);
        if(!ctype_alnum($text)){
            return FALSE;
	 
	 }
         else{ 
            $nome_valida = array (0=>48,1=>49,2=>50,3=>51,4=>52,5=>53,6=>54,7=>55,8=>56,9=>57,);
                   $str = $text;
                   $long = strlen ($str)-1;
                   for ($i = 0; $i <= $long; $i++) {
                           for ($x = 0; $x < count($nome_valida); $x++) {
                                   if (ord ($str[$i]) == $nome_valida[$x]) {
                                       
                                       $erro = true;
                                   }

                           }
                   }
	}
	if ($erro) {
		 
		return FALSE;
	}
       
        
        else if($strText == ""){
            return FALSE;
        }
       
        else{
            return TRUE;
        }
    }
    
    /**
     * 
     * @param string $senha
     * @param int $tam_min
     * @return boolean
     */
    private function valida_senha($senha,$tam_min = 6){
        
        if($senha == ""){
            return FALSE;
        }
        else if(strlen ($senha) < $tam_min){
            return FALSE;
        }
        else{
            return TRUE;
        }
    }
    
    /**
     * 
     * @param string $strText
     * @return boolean
     */
    private function valida_endereco($strText){
        
        
        $conversao = array('ÃƒÂ¡' => 'a','ÃƒÂ ' => 'a','ÃƒÂ£' => 'a','ÃƒÂ¢' => 'a', 'ÃƒÂ©' => 'e',
         'ÃƒÂª' => 'e', 'ÃƒÂ­' => 'i', 'ÃƒÂ¯'=>'i', 'ÃƒÂ³' => 'o', 'ÃƒÂ´' => 'o', 'ÃƒÂµ' => 'o', "ÃƒÂ¶"=>"o",
         'ÃƒÂº' => 'u', 'ÃƒÂ¼' => 'u', 'ÃƒÂ§' => 'c', 'ÃƒÂ±'=>'n', 'Ãƒï¿½' => 'A', 'Ãƒâ‚¬' => 'A', 'ÃƒÆ’' => 'A',
         'Ãƒâ€š' => 'A', 'Ãƒâ€°' => 'E', 'ÃƒÅ ' => 'E', 'Ãƒï¿½' => 'I', 'Ãƒï¿½'=>'I', "Ãƒâ€“"=>"O", 'Ãƒâ€œ' => 'O',
         'Ãƒâ€�' => 'O', 'Ãƒâ€¢' => 'O', 'ÃƒÅ¡' => 'U', 'ÃƒÅ“' => 'U', 'Ãƒâ€¡' =>'C', 'Ãƒâ€˜'=>'N');
        
        $text=(trim($strText));
        $text=str_replace(" ","",$text);
        $text = strtr($text, $conversao);
        if(!ctype_alnum($text)){
            return FALSE;
	 
	 }else if($strText == ""){
             return FALSE;
         }else{
             return TRUE;
         }
    }
    
    /**
     * 
     * @param string $cpf
     * @return boolean
     */
    private function valida_CPF($cpf = null) {
        
        // Verifica se um nÃƒÂºmero foi informado
        if(empty($cpf)) {
            return false;
        }

        // Elimina possivel mascara
        $cpf = ereg_replace('[^0-9]', '', $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
        

        
        // Verifica se o numero de digitos informados ÃƒÂ© igual a 11 
        if (strlen($cpf) != 11) {
            return false;
        }
        // Verifica se nenhuma das sequÃƒÂªncias invalidas abaixo 
        // foi digitada. Caso afirmativo, retorna falso
        else if ($cpf == '00000000000' || 
            $cpf == '11111111111' || 
            $cpf == '22222222222' || 
            $cpf == '33333333333' || 
            $cpf == '44444444444' || 
            $cpf == '55555555555' || 
            $cpf == '66666666666' || 
            $cpf == '77777777777' || 
            $cpf == '88888888888' || 
            $cpf == '99999999999') {
            return false;
         // Calcula os digitos verificadores para verificar se o
         // CPF ÃƒÂ© vÃƒÂ¡lido
         } else {   

            for ($t = 9; $t < 11; $t++) {

                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{$c} != $d) {
                    return false;
                }
            }

            return true;
        }
    }
    
    /**
     * 
     * @param string $date
     * @return boolean
     */
    private function valida_data($date) {

        if(empty($date)){            
            return FALSE;
        }else{

            $data = explode("-",$date); // fatia a string $dat em pedados, usando / como referencia
             $d = $data[0];
             $m = $data[1];
             $y = $data[2];
         
            // verifica se a data não válida!
            // 1 = true (válida)
            // 0 = false (inválida)
            $res = checkdate($d,$m,$y);

            if($d > 31 || $m > 12 || $y < 1){
                return false;
            }else if ($res == 1){
                return TRUE;
            } else {
                return FALSE;
            }
        }
        
    }
	
    /**
     * 
     * @param string $ip
     */
    private function  valida_ip($ip){
        //print "d";
        if(filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
            
            return FALSE;
        } else {
            return TRUE;
        }
        
        
    }

    /**
     * 
     * @param string $mac
     * @return boolean
     */
    private function valida_mac($mac) {
        if (strlen($mac) > 17 or strlen($mac) < 17 ){
            return FALSE;    
                   }
        else if (($mac[2] !=":") || ($mac[5] !=":" ) || ($mac[8] !=":") || ($mac[11] !=":" )|| ($mac[14] !=":")) {
            
            //print $mac;
            return FALSE;     
                        
        }
        $erro=TRUE;
        $M=$mac;
        $mac1=  str_replace(":", "", $mac);
        $array = array (0=>0,1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>'A',11=>'B',12=>'C',13=>'D',14=>'E',15=>'F');
        $str = $mac1;
        $long = strlen ($str);
        
                   
        for ($i = 0; $i < $long; $i++) {
                                    //print "contar".$str[$i];
                                    
            if  ($str[$i] == '0' || $str[$i] == '1' ||$str[$i] ==  '2' ||$str[$i] == '3' ||$str[$i] ==  '4' ||$str[$i] ==  '5' ||$str[$i] ==  '6' ||$str[$i] ==  '7' ||$str[$i] == '8' ||$str[$i] ==  '9' 
                ||$str[$i] ==  'A' ||$str[$i] ==  'B' ||$str[$i] == 'C'||$str[$i] ==  'D'||$str[$i] ==  'E' ||$str[$i] =='F' ) {
                //print $str.'\s';
                //print $i."\s";
                $erro = FALSE;
                //return FALSE;
                                       
            }
            else{
                $erro=TRUE;
                //return FALSE;
                //return FALSE;
            }

                           
        }
                   
        return TRUE;
	// verifica se a data ÃƒÂ© vÃƒÂ¡lida!
	// 1 = true (vÃƒÂ¡lida)
	// 0 = false (invÃƒÂ¡lida)
	
    }
    
   
    function valida_fone($fone){
     if(!preg_match('^\(+[0-9]{2,3}\) [0-9]{4}-[0-9]{4}$^', $fone)){

       return FALSE;
     }
    else {
         return TRUE;
     }
       
    }
    
    function  valida_cep($cep){
        if(!preg_match("/^[0-9]{5}([-][0-9]{3})$/",$_POST["cep"])){
            
            return FALSE;
        }
        else {
            return TRUE;
        }
        
    }
    function  valida_login($login){
        $long = strlen ($login);
        for ($i = 0; $i < $long; $i++) {
            if($login[$i]=='"' || $login[$i] =='@' || $login[$i]=='!' || $login[$i] =='#' || $login[$i]=='#' || $login[$i] =='$' || $login[$i]=='%' || $login[$i] =='Ã‚Â¨' ||
            $login[$i]=='&' || $login[$i] =='*' || $login[$i]=='(' || $login[$i] ==')' || $login[$i]=='+' || $login[$i] =='=' || $login[$i]=='/' || $login[$i] =='*' 
                    ){
                
                        return FALSE;
            }
       }
       
       return TRUE;
    }

    
}