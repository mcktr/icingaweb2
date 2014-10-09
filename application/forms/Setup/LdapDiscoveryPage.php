<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Form\Setup;

use Icinga\Protocol\Dns;
use Icinga\Web\Form;
use Icinga\Web\Form\Element\Note;
use Icinga\Form\LdapDiscoveryForm;
use Icinga\Form\Config\Resource\LdapResourceForm;
use Icinga\Web\Request;

/**
 * Wizard page to define the connection details for a LDAP resource
 */
class LdapDiscoveryPage extends Form
{
    /**
     * @var LdapDiscoveryForm
     */
    private $discoveryForm;

    /**
     * Initialize this page
     */
    public function init()
    {
        $this->setName('setup_ldap_discovery');
    }

    /**
     * @see Form::createElements()
     */
    public function createElements(array $formData)
    {
        $this->addElement(
            new Note(
                'description',
                array(
                    'value' => t(
                        'You can use this page to discover LDAP or ActiveDirectory servers ' .
                        ' for authentication. If you don\' want to execute a discovery, just skip this step.'
                    )
                )
            )
        );

        $this->discoveryForm = new LdapDiscoveryForm();
        $this->addElements($this->discoveryForm->createElements($formData)->getElements());

        $this->addElement(
            'checkbox',
            'skip_validation',
            array(
                'required'      => true,
                'label'         => t('Skip'),
                'description'   => t('Do not discover LDAP servers and enter all settings manually.')
            )
        );
    }

    /**
     * Validate the given form data and check whether a BIND-request is successful
     *
     * @param   array   $data   The data to validate
     *
     * @return  bool
     */
    public function isValid($data)
    {
        if ($data['skip_validation'] === '1') {
            return true;
        }
        if (false === parent::isValid($data)) {
            return false;
        }
        if (false === $this->discoveryForm->isValid($data)) {
            return false;
        }
        return true;
    }

    public function getValues($suppressArrayNotation = false)
    {
        if (! isset($this->discoveryForm) || ! $this->discoveryForm->hasSuggestion()) {
            return null;
        }
        return array(
            'domain' => $this->getValue('domain'),
            'type' => $this->discoveryForm->isAd() ?
                    LdapDiscoveryConfirmPage::TYPE_AD : LdapDiscoveryConfirmPage::TYPE_MISC,
            'resource' => $this->discoveryForm->suggestResourceSettings(),
            'backend' => $this->discoveryForm->suggestBackendSettings()
        );
    }
}
