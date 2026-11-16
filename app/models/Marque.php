<?php
/** Table MARQUE (idMarque, nom). */
class Marque extends ReferenceModel
{
    protected const TABLE = 'MARQUE';
    protected const CLE   = 'idMarque';
    protected const CHAMP = 'nom';
    protected const TRI   = 'nom';
}
