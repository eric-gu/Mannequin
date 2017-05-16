<?php


namespace LastCall\Patterns\Html\Extension;


use LastCall\Patterns\Core\Extension\AbstractExtension;
use LastCall\Patterns\Core\Metadata\YamlFileMetadataFactory;
use LastCall\Patterns\Html\Discovery\HtmlDiscovery;
use LastCall\Patterns\Html\Render\HtmlRenderer;
use Symfony\Component\Finder\Finder;

class HtmlExtension extends AbstractExtension {

  public function __construct(array $values = array()) {
    parent::__construct($values);
    $this['finder'] = function() {
      $finder = new Finder();
      $finder->files();
      $finder->name('*.html');
      return $finder;
    };
    $this['metadata_parser'] = function() {
      $config = $this->getConfig();
      return new YamlFileMetadataFactory($config->getVariableFactory());
    };
    $this['discovery'] = function() {
      return new HtmlDiscovery($this['finder'], $this['metadata_parser']);
    };
    $this['renderer'] = function() {
      $config = $this->getConfig();
      return new HtmlRenderer($config->getStyles(), $config->getScripts());
    };
  }

  public function in($dirs) {
    return $this->extend('finder', function(Finder $finder) use($dirs) {
      $finder->in($dirs);
      return $finder;
    });
    return $this;
  }

  public function getDiscoverers(): array {
    return [ $this['discovery'] ];
  }

  public function getRenderers(): array {
    return [ $this['renderer'] ];
  }
}