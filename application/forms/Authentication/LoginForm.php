<?php
// @codeCoverageIgnoreStart
// {{{ICINGA_LICENSE_HEADER}}}
/**
 * This file is part of Icinga Web 2.
 *
 * Icinga Web 2 - Head for multiple monitoring backends.
 * Copyright (C) 2013 Icinga Development Team
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @copyright  2013 Icinga Development Team <info@icinga.org>
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GPL, version 2
 * @author     Icinga Development Team <info@icinga.org>
 *
 */
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Form\Authentication;

use Icinga\Web\Form;

/**
 * Class LoginForm
 */
class LoginForm extends Form
{
    /**
     * Disable CSRF protection
     * @var bool
     */
    protected $tokenDisabled = true;

    /**
     * Interface how the form should be created
     */
    protected function create()
    {
        $this->setName('form_login');
        $this->addElement(
            'text',
            'username',
            array(
                'required' => true,
                'placeholder' => t('Username'),
            )
        );

        $this->addElement(
            'password',
            'password',
            array(
                'placeholder'    => t('Password'),
                'required' => true
            )
        );
        $user = $this->getElement('username');
        // TODO: We need a place to intercept filled forms before rendering
        if (isset($_POST['username'])) {
            $this->getElement('password')->setAttrib('class', 'autofocus');
        } else {
            $user->setAttrib('class', 'autofocus');
        }
        $this->setSubmitLabel('Login');

    }
}
// @codeCoverageIgnoreEnd
