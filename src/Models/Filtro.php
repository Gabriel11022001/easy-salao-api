<?php

namespace Models;

abstract class Filtro {

    public abstract function validarFiltro();

    public abstract function filtrar();

    /**
     * @return array
     */
    public abstract function getDados();

}