<script setup>
import {useForm} from "@inertiajs/vue3";
import {ref, watch} from "vue";
import VuePictureCropper, { cropper } from 'vue-picture-cropper';
import { Modal } from '@inertiaui/modal-vue';
import {Button} from "@/components/ui/button/index.js";
import {Upload, Save, Crop, X} from "lucide-vue-next";
import {AspectRatio} from "@/components/ui/aspect-ratio/index.js";
import {cn} from "@/lib/utils.js";
import {Input} from "@components/ui/input/index.js";
import ErrorMessage from "@components/shared/ErrorMessage.vue";
import {showToastsFromFormData} from "@/composables/useToasts.js";

const props = defineProps({
  imageUrl: [String, null],
  saveRoute: {
    type: String,
    required: true,
  },
  aspectRatio: {
    type: Number,
    default: 1,
  },
})
const form = useForm({
  image: props.imageUrl ?? null,
  _method: 'PATCH',
})

const modalRef = ref(null)
const currentImageUrl = ref('');
const showCropper = ref(false);
const uploadInput = ref(null);
const originalFile = ref(null);

// Инициализация
watch(() => form.image, (value) => {
  if (typeof value === 'string') {
    currentImageUrl.value = value;
  } else if (value instanceof File) {
    originalFile.value = value; // Сохраняем исходный файл
    readFile(value).then(url => currentImageUrl.value = url);
  } else {
    currentImageUrl.value = '';
    originalFile.value = null;
  }
}, { immediate: true });

// Выбор файла
function fileChange() {
  uploadInput.value.click();
  uploadInput.value.value = '';
}

// Обработка выбранного файла
function handleFileChange(e) {
  const file = e.target.files[0];

  // Если файл не выбран (отмена), оставляем предыдущее состояние
  if (!file) return;

  // Освобождаем предыдущий blob URL
  if (currentImageUrl.value.startsWith('blob:')) {
    URL.revokeObjectURL(currentImageUrl.value);
  }

  originalFile.value = file;
  form.image = file; // Сразу сохраняем в форму

  readFile(file).then(url => {
    currentImageUrl.value = url;
    showCropper.value = true;
  });
}

function readFile(file) {
  return new Promise((resolve) => {
    const reader = new FileReader();
    reader.onload = (e) => resolve(e.target.result);
    reader.readAsDataURL(file);
  });
}

async function applyCrop() {
  if (!cropper || !originalFile.value) return;

  // Получаем обрезанное изображение в исходном формате
  const blob = await cropper.getBlob(originalFile.value.type); // Важно: передаем исходный MIME-тип
  const file = new File([blob], originalFile.value.name, {
    type: originalFile.value.type // Сохраняем исходный тип
  });

  currentImageUrl.value = URL.createObjectURL(blob);
  form.image = file;
  showCropper.value = false;
}

const save = () => {
  return form.post(props.saveRoute, {
    forceFormData: true,
    onSuccess: (data) => {
      showToastsFromFormData(data)
      modalRef.value.close()
    }
  })
}

function isNewImage() {
  return props.imageUrl !== currentImageUrl.value;
}
</script>

<template>
  <Modal max-width="2xl" ref="modalRef">
    <div :class="cn('space-y-4 min-w-44 p-4', props.class)">

      <ErrorMessage :message="form.errors.image"/>

      <!-- Предпросмотр -->
      <template v-if="currentImageUrl && !showCropper">
        <AspectRatio :ratio="aspectRatio" class="bg-muted rounded-md">
          <img
            :src="currentImageUrl"
            class="object-cover w-full h-full rounded-md"
            alt="Предпросмотр"
          />
        </AspectRatio>

        <div class="flex items-center justify-between mt-6">
          <Button variant="outline" @click="fileChange()">
            <Upload class="w-4 h-4" />
            Выбрать другое
          </Button>
          <Button v-if="isNewImage()" @click="save()">
            <Save />
            Сохранить
          </Button>
        </div>

      </template>

      <!-- Кроппер -->
      <template v-else-if="currentImageUrl && showCropper">
        <VuePictureCropper
          :img="currentImageUrl"
          :options="{
            aspectRatio: aspectRatio,
            viewMode: 1,
            autoCrop: true
          }"
          class="h-full rounded-md"
        />

        <div class="flex items-center justify-between mt-6">
          <Button variant="outline" @click="showCropper = false">
            <X />
            Без обрезки
          </Button>
          <Button @click="applyCrop">
            <Crop />
            Обрезать
          </Button>
        </div>
      </template>

      <!-- Зона выбора изображения -->
      <template v-else>
        <AspectRatio :ratio="aspectRatio" @click="fileChange()" class="bg-muted rounded-md">
          <div class="flex items-center justify-center h-full text-muted-foreground rounded-md">
            <Upload class="w-4 h-4" />
            Выбрать изображение
          </div>
        </AspectRatio>
      </template>

    </div>

    <!-- Кнопка выбора изображения (скрытая) -->
    <input
      ref="uploadInput"
      type="file"
      accept="image/*"
      @change="handleFileChange"
      class="hidden"
    >
  </Modal>
</template>