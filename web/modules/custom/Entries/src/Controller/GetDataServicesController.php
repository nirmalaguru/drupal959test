<?php
namespace Drupal\entries\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;
use Drupal\Core\Form\FormBuilder;

/**
 * An example controller.
 */
class GetDataServicesController extends ControllerBase {
  /**
   * The node data.
   *
   * @var \Drupal\entries\GetDataServices
  */
  protected $node_data;

  /**
   * ExampleController constructor.
   *
   * @param \Drupal\entries\GetDataServices $node_data
   *   The form builder.
  */
  public function __construct($node_data) {
    $this->nodeData  = $node_data;
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The Drupal service container.
   *
   * @return static
   */

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entries.get_data_user_node')
    );
  }
  /**
   * Callback for modal form.
   */
  public function dynamusers(){
   // Custom service injected like this --
    
    $all_node = $this->nodeData->drupalise();
    $header = [
      'uid' => t('Uid'),
      'title' => t('title'),
      'type' => t('Type'),
    ];
    $rows = [];
    if(!empty($all_node)){
      foreach ($all_node as $key => $value) {
        $rows[] = [$value->nid, $value->title,$value->type];
      }
    }else{
      $rows[] = ['No Record Found'];
    }
    return [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];   
  }
  public function getexternalapis(){
    $url = 'https://api.publicapis.org/entries';
    $response = \Drupal::httpClient()->get($url);
    $data = json_decode($response->getBody(), TRUE);
   
	    foreach ($data as $element) {
  		$i=0;
		   foreach ($element as $elements){

				$node = Node::create([
				'type' => 'entries',
				'langcode' => 'en',
				'title' => $elements['API'],
				'body' => [
				'summary' => '',
				'value' => $elements['Description'],
				'format' => 'full_html',
				],
				]);	
				$node->save();
				if ($i===60) {
				break;
				}
				$i++;
			}
		}
	}
}
