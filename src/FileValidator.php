<?php

namespace Mix\Validate;

/**
 * FileValidator类
 * @author liu,jian <coder.keda@gmail.com>
 */
class FileValidator extends BaseValidator
{

    // 初始化选项
    protected $_initOptions = ['upload'];

    // 启用的选项
    protected $_enabledOptions = ['mimes', 'maxSize'];

    // 验证器名称
    protected $_name = '文件';

    // 初始化事件
    public function onInitialize()
    {
        parent::onInitialize(); // TODO: Change the autogenerated stub
        // 获取文件信息
        if(empty($this->attributeValue['tmp_name']) || empty($this->attributeValue['name']) || empty($this->attributeValue['type'])) {
            $this->attributeValue = \Mix::$app->request->files($this->attribute);
        }
    }

    // 上传验证
    protected function upload()
    {
        $value = $this->attributeValue;
        if ($value['error'] > 0) {
            // 设置错误消息
            switch ($value['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $defaultMessage = "上传的{$this->_name}大小超过了 php.ini 中 upload_max_filesize 选项限制的值.";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $defaultMessage = "上传的{$this->_name}大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $defaultMessage = "{$this->_name}只有部分被上传.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $defaultMessage = "没有{$this->_name}被上传.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $defaultMessage = '找不到临时文件夹.';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $defaultMessage = '上传写入失败.';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $defaultMessage = '上传扩展错误.';
                    break;
                default:
                    $defaultMessage = '未知上传错误.';
                    break;
            }
            $this->setError(__FUNCTION__, $defaultMessage);
            // 返回
            return false;
        }
        return true;
    }

    // MIME类型验证
    protected function mimes($param)
    {
        $value = $this->attributeValue;
        if (!in_array($value['type'], $param)) {
            // 设置错误消息
            $defaultMessage = "{$this->attribute}类型不在" . implode(',', $param) . "范围内.";
            $this->setError(__FUNCTION__, $defaultMessage);
            // 返回
            return false;
        }
        return true;
    }

    // 最大文件大小效验
    protected function maxSize($param)
    {
        $value = $this->attributeValue;
        if ($value['size'] > $param * 1024) {
            // 设置错误消息
            $defaultMessage = "{$this->attribute}不能大于{$param}KB.";
            $this->setError(__FUNCTION__, $defaultMessage);
            // 返回
            return false;
        }
        return true;
    }

}
