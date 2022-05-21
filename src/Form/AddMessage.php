<?php
namespace App\Form;

use App\Manager\ForumDatabase;
use App\Manager\Database;
use App\Model\Forum\Message;
use App\URL\CreateUrl;
use DateTime;

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

    public function createMessage(array $dataTopic): void
    {
        $arrayMessage   = [
            'idSubCat'          => $dataTopic['idSubCat'],
            'idTopic'           => $dataTopic['idTopic'],
            'idMember'          => $dataTopic['idMember'],
            'message'           => $this->data['message'],
            'dateTimeMessage'   => date('Y-m-d H:i:s')
        ];
        $message        = new Message();
        Database::hydrate($message, $arrayMessage);

        $id = $this->create([
            'id_sub_categories' => $message->getIdSubCat(),
            'id_topic'          => $message->getIdTopic(),
            'id_members'        => $message->getIdMember(),
            'message'           => $message->getMessage(),
            'date_time_message' => $message->getDateTimeMessage()->format('Y-m-d h:i:s')
        ], 'f_messages');

        $message->setId($id);
        $this->message = $message;
    }

    private function arrayDataMessage(array $dataTopic): array
    {
        $idSubCat   = $dataTopic['idSubCat'];
        $idTopic    = $dataTopic['idTopic'];
        $idMember   = $dataTopic['idMember'];
        $date       = date('Y-m-d H:i:s');
    
        $dataMessage = [
            'idSubCat'          => $idSubCat,
            'idTopic'           => $idTopic,
            'idMember'          => $idMember,
            'message'           => $data['message'],
            'dateTimeMessage'   => $date
        ];
        return $dataMessage;
    }

    public function redirectForm(string $cat, string $subCat, string $topic, int $idTropic, bool $success = false)
    {   
        $catUrl = CreateUrl::urlTitle($cat);
        $subCatUrl = CreateUrl::urlTitle($subCat);
        $topicUrl = CreateUrl::urlTitleTopic($topic);

        if ($success){
            header('Location: ' . CreateUrl::url("forum/{$catUrl}/{$subCatUrl}/{$topicUrl}-{$idTropic}?success=1"));
            exit();
        }
        header('Location: ' . CreateUrl::url("forum/{$catUrl}/{$subCatUrl}/{$topicUrl}-{$idTropic}"));
        exit();
    }
}