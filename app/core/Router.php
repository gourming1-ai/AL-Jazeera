<?php
/**
 * Routeur : associe une URL réécrite (.htaccess) à une action de contrôleur.
 *
 * Exemple : $router->get('parfums/{id}', 'ParfumController@show');
 * Jokers : {id} = nombre, {slug} = texte d'URL, {ressource} = donnée de référence du back-office.
 */
class Router
{
    private const JOKERS = [
        '{id}'        => '(\d+)',
        '{slug}'      => '([a-z0-9-]+)',
        '{ressource}' => '(marques|familles|fragrances|eaux|contenances)',
    ];

    /** @var array<string, array<int, array{0:string,1:string}>> */
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $chemin, string $action): void
    {
        $this->routes['GET'][] = [$this->enRegex($chemin), $action];
    }

    public function post(string $chemin, string $action): void
    {
        $this->routes['POST'][] = [$this->enRegex($chemin), $action];
    }

    private function enRegex(string $chemin): string
    {
        $regex = strtr(preg_quote(trim($chemin, '/'), '#'), array_combine(
            array_map(fn ($j) => preg_quote($j, '#'), array_keys(self::JOKERS)),
            array_values(self::JOKERS)
        ));
        return '#^' . $regex . '$#';
    }

    /** Cherche la route correspondante et appelle le contrôleur. */
    public function dispatch(string $methode, string $chemin): void
    {
        $methode = $methode === 'HEAD' ? 'GET' : $methode;
        $chemin  = trim($chemin, '/');

        foreach ($this->routes[$methode] ?? [] as [$regex, $action]) {
            if (preg_match($regex, $chemin, $m)) {
                array_shift($m);
                [$classe, $fonction] = explode('@', $action);
                $params = array_map(fn ($v) => ctype_digit($v) ? (int) $v : $v, $m);
                (new $classe())->$fonction(...$params);
                return;
            }
        }

        (new ErreurController())->introuvable();
    }
}
