<script setup>
import { MilkdownProvider } from "@milkdown/vue"
import CrepeEditor from "./CrepeEditor.vue"
import { ProsemirrorAdapterProvider } from '@prosemirror-adapter/vue'

const props = defineProps({
        modelValue: {
            type: String,
            default: ''
        },
        placeholder: {
            type: String,
            default: ''
        },
        autofocus: {
            type: Boolean,
            default: false
        },
        mentionQuery: {
            type: Object,
            default: () => ({})
    },
    disable_mention: {
        type: Boolean,
        default: false
    },
    disable_media: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue', 'handleMediaUpload'])

const handleUpdateValue = (value) => {
    emit('update:modelValue', value)
        }

const handleMediaUpload = (file) => {
    emit('handleMediaUpload', file)
        }
</script>

<template>
    <MilkdownProvider>
        <ProsemirrorAdapterProvider>
            <CrepeEditor 
                :mentionQuery="mentionQuery" 
                :autofocus="autofocus" 
                :placeholder="placeholder || 'Type your message here!'" 
                :disable_mention="disable_mention"
                :disable_media="disable_media"
                @update:modelValue="handleUpdateValue" 
                @handleMediaUpload="handleMediaUpload"
                :modelValue="modelValue"
            />
        </ProsemirrorAdapterProvider>
    </MilkdownProvider>
</template>
