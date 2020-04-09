<?php

namespace Drupal\entity_autocomplete\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\Element\EntityAutocomplete;

/**
 * Form to handle article autocomplete.
 */
class AutocompleteForm extends ConfigFormBase
{

    /**
     * The node storage.
     *
     * @var \Drupal\node\NodeStorage
     */
    protected $nodeStorage;

    const SETTINGS = 'entity.settings';


    /**
     * {@inheritdoc}
     */
    public function __construct(EntityTypeManagerInterface $entity_type_manager)
    {
        $this->nodeStorage = $entity_type_manager->getStorage('node');
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('entity_type.manager')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'custom_entity_autocomplete';
    }


    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            static::SETTINGS,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config(static::SETTINGS);
        $form['node'] = [
            '#type' => 'textfield',
            '#title' => $this->t('My Autocomplete'),
            '#autocomplete_route_name' => 'autocomplete.node',
            '#default_value' => $config->get('node'),
        ];

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        EntityAutocomplete::extractEntityIdFromAutocompleteInput($form_state->getValue('node'));
        $this->config(static::SETTINGS)
            ->set('node', $form_state->getValue('node'))
            ->save();
    }
}