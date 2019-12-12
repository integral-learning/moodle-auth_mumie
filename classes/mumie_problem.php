<?php
/**
 * MumieTask plugin
 *
 * @copyright   2019 integral-learning GmbH (https://www.integral-learning.de/)
 * @author      Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_mumie;

defined('MOODLE_INTERNAL') || die;
class mumie_problem implements \JsonSerializable
{
    private $link;
    private $headline;
    private $languages = array();
    private $tags = array();

    /**
     * Get the value of headline
     */
    public function get_headline()
    {
        return $this->headline;
    }

    /**
     * Set the value of headline
     *
     * @return  self
     */
    public function set_headline($headline)
    {
        $this->headline = $headline;

        return $this;
    }

    public function __construct($task)
    {
        $this->link = $task->link;
        $this->headline = $task->headline;
        if (isset($task->tags)) {
            $this->tags = $task->tags;
        }
        $this->collect_languages();
    }

    public function collect_languages()
    {
        if ($this->headline) {
            foreach ($this->headline as $langitem) {
                array_push($this->languages, $langitem->language);
            }
        }
    }
    /**
     * Get the value of link
     */
    public function get_link()
    {
        return $this->link;
    }

    /**
     * Set the value of link
     *
     * @return  self
     */
    public function set_link($link)
    {
        $this->link = $link;

        return $this;
    }

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);

        return $vars;
    }

    /**
     * Get the value of languages
     */
    public function get_languages()
    {
        return $this->languages;
    }

    /**
     * Set the value of languages
     *
     * @return  self
     */
    public function set_languages($languages)
    {
        $this->languages = $languages;

        return $this;
    }

    /**
     * Get the value of tags
     */
    public function get_tags()
    {
        return $this->tags;
    }
}
