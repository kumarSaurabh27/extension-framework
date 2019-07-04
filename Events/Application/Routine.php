<?php

namespace Webkul\UVDesk\ExtensionFrameworkBundle\Events\Application;

class Routine
{
    const PREPARE_DASHBOARD = 'uvdesk_extensions.application_routine.prepare_dashboard';

    const HANDLE_API_REQUEST = 'uvdesk_extensions.application_routine.handle_api_request';

    const HANDLE_CALLBACK_REQUEST = 'uvdesk_extensions.application_routine.handle_callback_request';

    // private function setApplication(\Webkul\AppBundle\Entity\Application $application)
    // {
    //     $this->application = $application;
    //     $this->setApplicationRouteName($application->getName());

    //     return $this;
    // }

    // public function getApplication()
    // {
    //     return $this->application;
    // }

    // private function setApplicationRouteName($applicationName)
    // {
    //     $this->applicationRouteName = str_replace(' ', '-', strtolower($applicationName));

    //     return $this;
    // }

    // public function getApplicationRouteName()
    // {
    //     return $this->applicationRouteName;
    // }

    // public function addEventResponse(array $response)
    // {
    //     $this->eventResponse = array_unique(array_merge($this->eventResponse, $response), SORT_REGULAR);
    //     return $this;
    // }

    // public function getEventResponse()
    // {
    //     return $this->eventResponse;
    // }

    // public function removeEventResponse($index)
    // {
    //     if (!empty($this->eventResponse[$index]))
    //         unset($this->eventResponse[$index]);

    //     return $this;
    // }

    // public function clearEventResponse()
    // {
    //     $this->eventResponse = [];
    //     return $this;
    // }

    // public function addEventData(array $data)
    // {
    //     $this->eventData = array_unique(array_merge($this->eventData, $data), SORT_REGULAR);
    //     return $this;
    // }

    // public function getEventData()
    // {
    //     return $this->eventData;
    // }

    // public function removeEventData($index)
    // {
    //     if (!empty($this->eventData[$index]))
    //         unset($this->eventData[$index]);

    //     return $this;
    // }

    // public function clearEventData()
    // {
    //     $this->eventData = [];
    //     return $this;
    // }

    // public function setSessionData($index, $indexData)
    // {
    //     $this->session->set($index, $indexData);

    //     return $this;
    // }

    // public function getSessionData($index)
    // {
    //     return $this->session->get($index);
    // }

    // public function removeSessionData($index)
    // {
    //     $this->session->remove($index);

    //     return $this;
    // }

    // public function raiseSessionMessage($messageType = 'warning', $sessionMessage)
    // {
    //     $this->session->getFlashBag()->add($messageType, $sessionMessage);

    //     return $this;
    // }
}
