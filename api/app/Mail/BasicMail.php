<?php

namespace App\Mail;

use App\Models\MailTemplate;
use App\Models\MailTemplateLocale;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Blade;

abstract class BasicMail extends Mailable
{
    protected $mail_template_locale = null;

    abstract protected function _toEmail();
    abstract protected function _toName();
 
    public function getToEmail()
    {
        return $this->_toEmail();
    }

    protected function _getTemplateCode()
    {
        return false;
    }

    protected function _getLocale()
    {
        return '';
    }

    protected function _getData()
    {
        return [];
    }

    protected function _getSubject()
    {
        if ($this->_getMailTemplate()) {
            return $this->mail_template_locale->subject;
        }
        return '';
    }

    protected function _getContent() 
    {
        if ($this->_getMailTemplate()) {
            $content = $this->mail_template_locale->content;
            $data = $this->_getData();
            if (!empty($data)) {
                $content = $this->_bladeCompile($content, $data);
            }
            return $content;
        }
        return '';
    }

    final protected function _getMailTemplate()
    {
        if ($this->mail_template_locale === null) {
            $this->_setMailTemplate();
        }        
        return $this->mail_template_locale;
    }

    final protected function _setMailTemplate()
    {
        $template_code = $this->_getTemplateCode();
        if ($template_code != false) {                
            $mail_template = MailTemplate::where('code', $template_code)->first();            
            if ($mail_template) {
                $mail_template_locale = MailTemplateLocale::where('mail_template_id', $mail_template->id)
                    ->where(function($query) {
                        $query->where('locale', $this->_getLocale())->orWhereNull('locale');
                    })
                    ->orderBy('locale', 'DESC')->first();
                if ($mail_template_locale) {
                    $this->mail_template_locale = $mail_template_locale;
                    return true;
                }                    
            }
        }
        $this->mail_template_locale = false;
        return false;
    }

    public function build()
    {        
        $this->to($this->_toEmail(), $this->_toName());
        $this->subject($this->_getSubject());
        $this->html($this->_getContent());        
    }    

    final protected function _bladeCompile($html, array $args = array())
    {
        $generated = Blade::compileString($html);
        
        ob_start() and extract($args, EXTR_SKIP);

        // We'll include the view contents for parsing within a catcher
        // so we can avoid any WSOD errors. If an exception occurs we
        // will throw it out to the exception handler.
        try
        {
            eval('?>'.$generated);
        }

        // If we caught an exception, we'll silently flush the output
        // buffer so that no partially rendered views get thrown out
        // to the client and confuse the user with junk.
        catch (\Exception $e)
        {
            ob_get_clean(); throw $e;
        }

        $content = ob_get_clean();

        return $content;
    }
}