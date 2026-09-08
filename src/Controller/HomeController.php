<?php
namespace App\Controller;

class HomeController {
    public function index() {
        // Define o fuso horário do Brasil
        date_default_timezone_set('America/Sao_Paulo');

        // Define locale para português do Brasil
        setlocale(LC_TIME, 'pt_BR.UTF-8', 'pt_BR', 'portuguese');

        $dataHora = date("d/m/Y H:i:s");

        return "<center>"
            . "<h1 style='color:darkblue;font-family:Arial;'>Bem-vindo ao Projeto MVC de Marcos!</h1>"
            . "<hr style='width:501px solid darkblue;'>"
            . "<p style='font-size:18px;color:gray;'>Servidor iniciado em : <b>$dataHora</b></p>"
        . "</center>";
    }
}
