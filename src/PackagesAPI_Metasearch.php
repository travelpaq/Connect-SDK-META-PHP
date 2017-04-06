<?php
/**
 * TravelPAQ Connect Api - Api para la búsqueda y reserva 
 * de paquetes turísticos de Tour Operadores
 *
 * @package  TravelPAQ
 * 
 * @author   Maximiliano Alves Pinheiro <malves@travelpaq.com.ar>
 * @author   TravelPAQ <malves@travelpaq.com.ar>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace TravelPAQ\PackagesAPI;

use TravelPAQ\PackagesAPI\Models\SearchData;
use TravelPAQ\PackagesAPI\Models\FilterData;

use TravelPAQ\PackagesAPI\Services\HttpClient;
use TravelPAQ\PackagesAPI\Services\PackageService;
use TravelPAQ\PackagesAPI\Services\TravelService;

use TravelPAQ\PackagesAPI\Models\Exceptions\ValidationException;
use TravelPAQ\PackagesAPI\Exceptions\JsonValidatorException;
/**
 * Class PackagesAPI_Metasearch
 *
 * @package TravelPAQ
 */
class PackagesAPI_Metasearch
{
    /**
     * Constructor.
     * 
     * - 'key' clave privada de la empresa de turismo para acceder al servicio
     *         otorgada por TravelPAQ.
     * - 'item_per_page' cantidad de items por pagina para pedir paquetes.
     *
     * @param mixed $config 
     */
    public function __construct($config) 
    {
        if(array_key_exists('test', $config) && $config['test'] == true){
            HttpClient::getInstance([
                'url' => 'http://travelpaq-connect-test.us-east-1.elasticbeanstalk.com/' . $config['version'] . '/',
                'key' => $config['api_key'],
                'item_per_page' => $config['item_per_page']
            ]);
        }else{
            HttpClient::getInstance([
                'url' => 'https://api.travelpaq.com.ar/'.$config['version'] .'/',
                'key' => $config['api_key'],
                'item_per_page' => $config['item_per_page']
            ]);
        }
    }

    /**
     * Obtiene un listado de paquetes en base a un conjunto de parámetros
     * que filtrán la búsqueda
     * 
     * @param Array $params Representa los parametros de búsqueda
     * 
     * @param int $page Número de página
     *
     * @param Array $filters Representa los filtros para hacer más precisa la búsqeuda
     * 
     * @return PackagesPagination Representa una página de resultado de búsqueda.
     */
    public function getPackageList($params,$page = 0, $filters = null)
    {
        $sd = new SearchData($params);
        if(!$sd->validate()){
            throw new ValidationException($sd->get_last_error());
        }
        $ps = new PackageService();
        if($filters){
            $fi = new FilterData($filters);
            if(!$fi->validate()){
                throw new ValidationException($fi->get_last_error());
            }
            return $ps->getPackageList($params,$page,$filters);
        }
        return $ps->getPackageList($params,$page); 
    }

   
    /**
     * Obtiene todos los destinos donde hay paquetes disponibles
     *
     * @param string|null $country_iata IATA del pais del cual se requieren los lugares
     *
     * @return Destinies Retorna un listado de lugares del pais solicitado, o todos los
     * destinos disponibles si el $country_iata no fue especificado o esta en null 
     *
     */
    public function getPlacesWithPackage($country_iata = null)  
    {
        $travelService = new TravelService();
        return $travelService->getPlacesWithPackage($country_iata);
    }

    /**
     * Obtiene todas los meses de salida para un IATA en particular
     *
     * @param string $place_iata IATA del lugar del cual se requieren los meses de salida
     *
     * @return Months Retorna un listado de meses en los que salen los paquetes de un 
     * destino determinado
     *
     */
    public function getMonthByPlaces($place_iata = null)  
    {
        $travelService = new TravelService();
        return $travelService->getMonthByPlaces($place_iata);
    }

    /**
     * Obtiene todas las tarifas para un IATA determinado y mes / año específico
     *
     * @param string $origin_place IATA del lugar de salida de los paquetes de los cuales se requieren las tarifas
     *
     * @param string $departure_place IATA del lugar de llegada de los paquetes de los cuales se requieren las tarifas
     *
     * @param int $month Mes del que se quieren averiguar las tarifas disponibles
     *
     * @param year $year Año del que se quieren averiguar las tarifas disponibles
     *
     * @return FaresPackage Retorna un listado de tarifas de paquetes
     *
     */
    public function getFaresPackage($origin_place = null, $departure_place = null, $month = null, $year = null)  
    {
        $travelService = new TravelService();
        return $travelService->getFaresPackage($origin_place, $departure_place, $month, $year);
    }

     /**
     * Obtiene todas las tarifas 
     *
     * @return Array PackageFares Retorna un listado de tarifas de paquetes
     *
     */
    public function getFaresTree()  
    {
        $travelService = new TravelService();
        return $travelService->getFaresTree();
    }

    public function getPlaces($country_iata = null)  
    {
        $travelService = new TravelService();
        return $travelService->getPlaces($country_iata);
    }

}