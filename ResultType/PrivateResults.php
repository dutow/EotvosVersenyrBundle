<?php

namespace Eotvos\VersenyrBundle\ResultType;

/**
 * Interface for user registration providers. 
 * 
 * @author    Zsolt Parragi <zsolt.parragi@cancellar.hu> 
 * @copyright 2012 Cancellar
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   Release: v0.1
 */
class PrivateResults implements ResultTypeInterface
{
    /**
     * Returns the HTML code of the result table or an error message.
     * 
     * @return string html code
     */
    public function generateHtml($round, $standing)
    {
        $twig = <<<ENDIT
<h3>{{ 'eotvos.results.title'|trans }}</h3>
<table>
    <thead>
        <tr>
            <th>{{ 'eotvos.versenyr.results.identifier'|trans }}</th>
            <th>{{ 'eotvos.versenyr.results.points'|trans }}</th>
            <th>{{ 'eotvos.versenyr.results.advances'|trans }}</th>
        </tr>
    </thead>
    <tbody>
        {% for st in standing %}
        {% if st[0] == user.uniqueIdentifier %}
        <tr>
            <td>{{ st[0] }}</td>
            <td>{% if st[2]!=0 %}{{ st[2] }}{% else %}-{% endif %}</td>
            <td>{{ ((loop.index <= round.advanceNo) ? 'eotvos.versenyr.results.yes' : 'eotvos.versenyr.results.no' )|trans }}</td>
        </tr>
        {% endif %}
        {% endfor %}
    </tbody>
</table>
ENDIT;

        $tpl = $this->container->get('twigstring');

        $user = $this->container->get('security.context')->getToken()->getUser();

        return $tpl->renderResponse($view, array('standing' => $standing, 'user' => $user, 'round' => $round));
    }

    /**
     * setContainer
     * 
     * @param mixed $container service container
     * 
     * @return void
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }
    

    public function getName()
    {
        return "PrivateResults";
    }
}




