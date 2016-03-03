<?php

namespace MaterialLib\Service\MaterialLib\Impl;

use MaterialLib\Service\MaterialLib\MaterialLibService;
use MaterialLib\Service\BaseService;
use Topxia\Common\ArrayToolkit;

class MaterialLibServiceImpl extends BaseService implements MaterialLibService
{
    public function search($conditions, $start, $limit)
    {
        $conditions['start'] = $start;
        $conditions['limit'] = $limit;
        $conditions = $this->filterConditions($conditions);
        return $this->getUploadFileService()->search($conditions, 'cloud');
    }

    protected function filterConditions($conditions)
    {
        $filterConditions = array_filter($conditions);

        if (!empty($filterConditions['createdUserId'])) {
            $filterConditions['endUser'] = $filterConditions['createdUserId'];
            unset($filterConditions['createdUserId']);
        }

        if (!empty($filterConditions['courseId'])) {
            $localFiles = $this->getUploadFileService()->findFilesByTypeAndId('courselesson', $filterConditions['courseId']);
            $globalIds = ArrayToolkit::column($localFiles, 'globalId');
            $filterConditions['nos'] = implode(',', $globalIds);
        }

        return $filterConditions;
    }

    protected function getUploadFileService()
    {
        return $this->createService('File.UploadFileService2');
    }
}
