<script setup>
import { ref, watch } from 'vue';
import VuePictureCropper, { cropper } from 'vue-picture-cropper';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog/index.js';
import { Button } from '@/components/ui/button/index.js';
import {ImageOff, Upload, X} from 'lucide-vue-next';
import {AspectRatio} from "@/components/ui/aspect-ratio/index.js";
import {cn} from "@/lib/utils.ts";

const props = defineProps({
  modelValue: [File, String, null],
  aspectRatio: {
    type: Number,
    default: 1,
  },
  class: {
    type: String,
    default: ''
  },
  emptyPreview: {
    type: Boolean,
    default: true,
  },
  uploadLabel: {
    type: String,
    default: 'Выбрать изображение',
  },
  removeLabel: {
    type: String,
    default: 'Удалить',
  },
});

const emit = defineEmits(['update:modelValue']);

const imageUrl = ref('');
const showCropper = ref(false);
const uploadInput = ref(null);
const originalFile = ref(null);

// Инициализация
watch(() => props.modelValue, (value) => {
  if (typeof value === 'string') {
    imageUrl.value = value;
  } else if (value instanceof File) {
    originalFile.value = value; // Сохраняем исходный файл
    readFile(value).then(url => imageUrl.value = url);
  } else {
    imageUrl.value = '';
    originalFile.value = null;
  }
}, { immediate: true });

function handleFileChange(e) {
  const file = e.target.files[0];
  if (!file) return;

  originalFile.value = file; // Сохраняем исходный файл
  readFile(file).then(url => {
    imageUrl.value = url;
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

  imageUrl.value = URL.createObjectURL(blob);
  emit('update:modelValue', file);
  showCropper.value = false;
}

function removeImage() {
  imageUrl.value = '';
  originalFile.value = null;
  emit('update:modelValue', null);

  // Сбрасываем значение input, чтобы можно было выбрать тот же файл снова
  if (uploadInput.value) {
    uploadInput.value.value = '';
  }

  // Освобождаем память от URL объекта, если он был создан
  if (imageUrl.value.startsWith('blob:')) {
    URL.revokeObjectURL(imageUrl.value);
  }
}
</script>

<template>
  <div :class="cn('space-y-4 min-w-44', props.class)">
    <!-- Предпросмотр -->
    <template v-if="imageUrl">
      <AspectRatio :ratio="aspectRatio" class="bg-muted rounded-md">
        <img
          :src="imageUrl"
          class="object-cover w-full h-full rounded-md"
          alt="Предпросмотр"
        />
      </AspectRatio>

      <Button type="button" variant="outline" @click="removeImage" class="gap-2 w-full">
        <ImageOff class="w-4 h-4" />
        {{removeLabel}}
      </Button>
    </template>

    <!-- Кнопка выбора -->
    <template v-else>
      <AspectRatio v-if="emptyPreview" :ratio="aspectRatio" class="bg-muted rounded-md">
        <div class="flex items-center justify-center h-full text-muted-foreground rounded-md">
          <ImageOff class="h-6 w-6" />
        </div>
      </AspectRatio>

      <Button
        variant="outline"
        type="button"
        @click="uploadInput.click()"
        class="gap-2 w-full"
      >
        <Upload class="w-4 h-4" />
        {{ uploadLabel }}
      </Button>
    </template>

  </div>

  <input
    ref="uploadInput"
    type="file"
    accept="image/*"
    @change="handleFileChange"
    class="hidden"
  >

  <!-- Диалог обрезки -->
  <Dialog v-model:open="showCropper">
    <DialogContent class="max-w-[80vw] h-[80vh] flex flex-col">
      <DialogHeader>
        <DialogTitle>Обрезка изображения</DialogTitle>
      </DialogHeader>

      <div class="flex-1 min-h-0">
        <VuePictureCropper
          :img="imageUrl"
          :options="{
            aspectRatio: aspectRatio,
            viewMode: 1,
            autoCrop: true
          }"
          class="h-full"
        />
      </div>

      <DialogFooter>
        <Button variant="outline" @click="showCropper = false">
          Отмена
        </Button>
        <Button @click="applyCrop">
          Применить
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>