<?php


namespace AppBundle\Command;


use AppBundle\Repository\RegistrosRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ObtenerDatosCommand extends Command
{
    private $registrosRepository;

    public function __construct(RegistrosRepository $registrosRepository)
    {
        parent::__construct();
        $this->registrosRepository = $registrosRepository;
    }

    protected function configure()
    {
        $this
            ->setName('app:obtener-datos')
            ->setDescription('Obtener los datos del fichero de Arduino de la estación meteorológica');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // RUTA DE LA PAGINA DE ARDUINO
        $paginaArduino = file_get_contents('web/estacion.php');

        $datosDevueltos = substr($paginaArduino, strpos($paginaArduino,'<body>')+6, strrpos($paginaArduino, '</body>') - strpos($paginaArduino,'<body>')-6);
        $conjuntoDatos = explode('<br />', $datosDevueltos);
        foreach ($conjuntoDatos as $dato){
            $valoresSinEspacios = trim($dato);
            $valores = explode(': ', $valoresSinEspacios);
            switch ($valores[0]) {
                case 'temperatura':
                    $temperatura = $valores[1];
                    break;
                case 'humedad':
                    $humedad = $valores[1];
                    break;
                case 'lluvia':
                    $lluvia = $valores[1];
                    break;
                case 'viento':
                    $viento = $valores[1];
                    break;
                case 'dirViento':
                    $dirViento = $valores[1];
                    break;
            }
        }
        $fecha = new \DateTime('now');

        $exito = $this->registrosRepository->grabarDatos($fecha, $temperatura, $humedad, $lluvia, $viento, $dirViento);

        $style = new SymfonyStyle($input, $output);
        $style->title('Obteniendo los datos de la estación');
        $style->text('Guardando la temperatura, humedad, lluvia, viento y dirección del viento obtenidas.');
        if($exito){
            $style->success('Datos guardados con éxito');
        }else{
            $style->success('Error al guardar los datos');
        }
    }
}