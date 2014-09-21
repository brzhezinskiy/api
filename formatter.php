<?php
/**
 * Реализация стандартного вывода в различные форматы
 * Реализация UserList0
 * Автор: freecod@mail.ru
*/

class DefaultFormatter
{
    protected $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }
    
    public function toJSON()
    {
        header('Content-Type: application/json; charset=utf8');
        return json_encode($this->data);
    }
      
    public function toArray()
    {
        return $this->data;
    }
    
    public function toHTML()
    {
        Header("Content-Type: text/html;charset=UTF-8");
        return '<h1>Нет модели отображения в данном формате!</h1>';
    }
    
    public function __toString()
    {
        if(is_array($this->data)){
            return implode(', ', $this->data);
        }
        return $this->data;
    }
    
    public function toXML()
    {
        header('Content-Type: application/xml; charset=utf8');
        $xml = new SimpleXMLElement('<root/>');
        self::to_xml($xml, $this->data);
        return $xml->asXML();
    }
    
    protected function to_xml(SimpleXMLElement $object, array $data)
    {   
        foreach ($data as $key => $value)
        {   
            if (is_array($value))
            {   
                //$new_object = $object->addChild( is_numeric($key) ? 'user'.$key : $key);
                $new_object = $object->addChild( is_numeric($key) ? 'user' : $key);
                $this->to_xml($new_object, $value);
            }   
            else
            {   
                $object->addChild( is_numeric($key) ? 'param' : $key , $value);
            }   
        }   
    }
}


/**
 * Реализации классов форматирования вывода UserModel
 * могут переопределять методы родительского класса
*/

class SelectUserByID extends DefaultFormatter 
{  
    public function toHTML()
    {
        $ret = '<table>';
        $ret .= '<tr>';
        foreach ($this->data as $item) {
            $ret .= '<td>' . $item . '</td>';
        }
        $ret .= '</tr>';
        $ret .= '</table>';
        return $ret;
    }
}


class SelectUsers extends DefaultFormatter 
{ 
    public function toHTML()
    {
        $ret = '<table>';
        foreach ($this->data as $val) {
            $ret .= '<tr>';
            foreach ($val as $item) {
                $ret .= '<td>' . $item . '</td>';
            }
            $ret .= '</tr>';
        }
        $ret .= '</table>';
        return $ret;
    }
}

class AddUser extends DefaultFormatter 
{ 
}

class UpdateUser extends DefaultFormatter 
{ 
}

class DeleteUser extends DefaultFormatter 
{ 
}

class GetTableStructure extends DefaultFormatter 
{ 
}

class FormattedError extends DefaultFormatter 
{ 
    public function __construct($error)
    {
        $this->data['error'] = 1;
        $this->data['message'] = $error;
    }
}









