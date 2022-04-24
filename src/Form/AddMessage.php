<?php
namespace App\Form;

use App\Manager\ForumDatabase;
use App\Manager\Database;
use App\Manager\Message;

class AddMessage extends AddData
{

    private $message;

    public function validateMessage(): self
    {
        $data = new Validator($this->data);
        $data->check('required', 'message');

        $this->resultValidator  = $data->validateForm();
        $this->errors           = $data->getErrors();
        
        return $this;
    }

    public function createMessage(array $data): void
    {
        $arrayMessage = $this->arrayDataMessage($data);
        $message = new Message();
        Database::hydrate($message, $arrayMessage);

        $id = $this->create([
            'id_sub_categories' => $message->getIdSubCat(),
            'id_topic'          => $message->getIdTopic(),
            'id_members'        => $message->getIdMembers(),
            'message'           => $message->getMessage(),
            'date_time_message' => $message->getDateTimeMessage()->format('Y-m-d h:i:s')
        ], 'f_messages');

        $message->setId($id);
        $this->message = $message;
    }

    private function arrayDataMessage(array $data): array
    {
        $idSubCat   = null;
        $idTopic    = null;
        $idMember   = null;
        $date       = date('Y-m-d H:i:s');
        
        $dataMessage = [
            'idSubCat'  => $idSubCat,
            'idTopic'   => $idTopic,
            'idMember'  => $idMember,
            'message'   => $data['message'],
            'dateTimeMessage' => $date
        ];

        return $dataTopic;

    }
}