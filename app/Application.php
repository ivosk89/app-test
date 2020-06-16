<?php

use Providers\DoctrineORMServiceProvider;
use Providers\MySQLKeeperServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class Application extends Silex\Application
{
    use Silex\Application\FormTrait;
    use Silex\Application\MonologTrait;
    use Silex\Application\SecurityTrait;
    use Silex\Application\SwiftmailerTrait;
    use Silex\Application\TwigTrait;
    use Silex\Application\UrlGeneratorTrait;

    public function init()
    {
        ErrorHandler::register();
        ExceptionHandler::register($this['debug']);

        $this->register(new UrlGeneratorServiceProvider());
        $this->register(new HttpFragmentServiceProvider());
        $this->register(new SessionServiceProvider());

        /* Database */
        $this->register(new DoctrineServiceProvider(), $this['options']['db']);
        $this->register(new DoctrineORMServiceProvider());
        $this->register(new MySQLKeeperServiceProvider(), $this['options']['mysqlkeep']);

        /* Frontend */
        $this->register(new FormServiceProvider());
        $this->register(new ValidatorServiceProvider());
        $this->register(new TwigServiceProvider(), $this['options']['twig']);

        /* Security */
        $this->register(new SecurityServiceProvider(), array_merge_recursive($this['options']['security'], array(
                'security.firewalls' => []
            ))
        );

        $this['security.encoder.digest'] = $this->share(function () {
            return new MessageDigestPasswordEncoder();
        });

        $this['security.encoder_factory'] = $this->share(function () {
            return new EncoderFactory(
                array(
                    'Symfony\Component\Security\Core\User\UserInterface' => $this['security.encoder.digest']
                )
            );
        });

        /* Cache, Logging */
        $this->register(new HttpCacheServiceProvider(), $this['options']['http_cache']);
        if(!file_exists(dirname($this['options']['monolog']['monolog.logfile']))){
            mkdir(dirname($this['options']['monolog']['monolog.logfile']));
        }
        $this->register(new MonologServiceProvider(), $this['options']['monolog']);

        /* i18n */
        $this->register(new TranslationServiceProvider());
    }

    public function route()
    {
        $loader = new YamlFileLoader(new FileLocator(DIR_CONFIG));
        $collection = $loader->load('routes.yml');
        \App::_('routes')->addCollection($collection);

        /* 404 page */
        \App::_()->error(function (\Exception $e, $code) {
            if (\App::_('debug') == true && $code != 404) {
                return null;
            }
            switch ($code) {
                case 404:
                    return \App::_('twig')->render('404.twig');
                    break;
                case 500:
                    return \App::_('twig')->render('500.twig', array(
                        'message' => $e->getMessage()
                    ));
                    break;
                default:
                    return null;
            }
        });
    }
}