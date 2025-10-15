<script setup>
import logoInverseUrl from "@img/logo-inverse.svg";

import Wrapper from "@components/shared/layout/Wrapper.vue";
import {Button} from "@components/ui/button/index.js";
import { Github } from 'lucide-vue-next';
</script>

<template>
  <footer class="bg-primary text-primary-foreground">
    <Wrapper>
      <div class="py-6 flex justify-between items-center">
        <div class="flex items-center">
          <img :src="logoInverseUrl" class="h-14 w-auto mr-4" alt="Free маркетплейс "/>
          Проект для демонстрации
        </div>
        <div>
          <a target="_blank" href="https://github.com/mozg404">
            <Button variant="outline" class="text-primary">
                <Github /> GitHub
            </Button>
          </a>
        </div>
      </div>
    </Wrapper>
  </footer>
</template>