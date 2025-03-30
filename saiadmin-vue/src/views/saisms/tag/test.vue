<template>
    <component
      is="a-modal"
      v-model:visible="visible"
      :width="600"
      title="短信测试发送"
      :mask-closable="false"
      :ok-loading="loading"
      @cancel="close"
      @before-ok="submit">
      <!-- 表单信息 start -->
      <a-form ref="formRef" :model="formData" :rules="rules" :auto-label-width="true">
        <a-row :gutter="10">
          <a-col :span="24">
            <a-form-item label="接收号码" field="mobile">
              <a-input v-model="formData.mobile" placeholder="请输入手机号码" />
            </a-form-item>
          </a-col>
        </a-row>
      </a-form>
      <!-- 表单信息 end -->
    </component>
  </template>
  
  <script setup>
  import { ref, reactive, computed } from 'vue'
  import { Message, Modal } from '@arco-design/web-vue'
  import api from '../api/tag'
  
  // 引用定义
  const visible = ref(false)
  const loading = ref(false)
  const formRef = ref()
  const mode = ref('')
  
  // 表单信息
  const formData = reactive({
    id: null,
    mobile: '',
    gateway: '',
    tag_name: ''
  })
  
  // 验证规则
  const rules = {
    mobile: [{ required: true, message: '接收号码必需填写' }],
  }
  
  // 打开弹框
  const open = async (type = 'add') => {
    mode.value = type
    visible.value = true
    formRef.value.resetFields()
    await initPage()
  }
  
  // 初始化页面数据
  const initPage = async () => {}
  
  // 设置数据
  const setFormData = async (data) => {
    for (const key in formData) {
      if (data[key] != null && data[key] != undefined) {
        formData[key] = data[key]
      }
    }
  }
  
  // 数据保存
  const submit = async (done) => {
    const validate = await formRef.value?.validate()
    if (!validate) {
      loading.value = true
      let data = { ...formData }
      const result = await api.testTag(data)
      if (result.code === 200) {
        Modal.info({
            title: '测试结果',
            width: '600px',
            bodyStyle: { height: '200px'},
            content: JSON.stringify(result.data)
        });
        done(true)
      }
      // 防止连续点击提交
      setTimeout(() => {
        loading.value = false
      }, 500)
    }
    done(false)
  }
  
  // 关闭弹窗
  const close = () => (visible.value = false)
  
  defineExpose({ open, setFormData })
  </script>
  