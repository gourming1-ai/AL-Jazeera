<?php
/** Table CONTENANCE (idContenance, libelle) : 30 ml, 50 ml… */
class Contenance extends ReferenceModel
{
    protected const TABLE = 'CONTENANCE';
    protected const CLE   = 'idContenance';
    protected const LIEN  = 'A_POUR_CONTENANCE';
    /** Tri numérique : 30 ml avant 100 ml. */
    protected const TRI   = 'CAST(libelle AS UNSIGNED), libelle';
}
