<?php


namespace LastCall\Mannequin\Core\Subscriber;


use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Pattern\TemplateFilePatternInterface;
use LastCall\Mannequin\Core\Variable\VariableFactoryInterface;
use LastCall\Mannequin\Core\YamlMetadataParser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class YamlFileMetadataSubscriber implements EventSubscriberInterface {

  public function __construct() {
    $this->parser = new YamlMetadataParser();
  }

  public static function getSubscribedEvents() {
    return [
      PatternEvents::DISCOVER => 'addYamlMetadata'
    ];
  }

  public function addYamlMetadata(PatternDiscoveryEvent $event) {
    $pattern = $event->getPattern();
    if($pattern instanceof TemplateFilePatternInterface && $file = $pattern->getFile()) {
      $yamlFile = $this->getYamlFileForPatternFile($pattern->getFile());
      if(file_exists($yamlFile)) {
        $metadata = $this->parser->parse(file_get_contents($yamlFile));
        if(empty($pattern->getName()) && $metadata['name']) {
          $pattern->setName($metadata['name']);
        }
        if(empty($pattern->getDescription()) && $metadata['description']) {
          $pattern->setDescription($metadata['description']);
        }
        $pattern->setVariableDefinition($metadata['definition']);
        foreach($metadata['tags'] as $k => $v) {
          $pattern->addTag($k, $v);
        }
        foreach($metadata['tags'] as $k => $v) {
          $pattern->addTag($k, $v);
        }
      }
    }
  }

  private function getYamlFileForPatternFile(\SplFileInfo $patternFile) {
    $path = $patternFile->getPath();
    $basename = $patternFile->getBasename('.' .$patternFile->getExtension()) . '.yml';
    return sprintf('%s%s%s', $path, DIRECTORY_SEPARATOR, $basename);
  }
}