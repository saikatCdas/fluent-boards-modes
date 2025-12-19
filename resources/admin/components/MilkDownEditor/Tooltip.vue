<script setup lang="ts">
import {TooltipProvider} from "@milkdown/kit/plugin/tooltip";
import {
    toggleEmphasisCommand,
    toggleStrongCommand
} from '@milkdown/kit/preset/commonmark';
import {callCommand} from '@milkdown/kit/utils';
import {useInstance} from '@milkdown/vue';
import {editorViewCtx, rootCtx} from '@milkdown/kit/core';

import {usePluginViewContext} from '@prosemirror-adapter/vue';
import {onMounted, onUnmounted, ref, VNodeRef, watch} from 'vue';

import {
    linkTooltipAPI,
    linkTooltipState
} from '@milkdown/kit/component/link-tooltip';

const {view, prevState} = usePluginViewContext()
const [loading, get] = useInstance()

const divRef = ref<VNodeRef>();

let tooltipProvider: TooltipProvider;

onMounted(() => {
    tooltipProvider = new TooltipProvider({
        content: divRef.value as any,
    })
    tooltipProvider.update(view.value, prevState.value)
})


watch(
    [view, prevState],
    () => {
        tooltipProvider?.update(view.value, prevState.value)
    }
)

onUnmounted(() => {
    tooltipProvider.destroy()
})

const toggleBold = (e: Event) => {
    if (loading.value) return;
    e.preventDefault()
    get()!.action(callCommand(toggleStrongCommand.key))
}

const toggleItalic = (e: Event) => {
    if (loading.value) return;
    e.preventDefault();
    get()!.action(callCommand(toggleEmphasisCommand.key))
}

</script>

<template>
    <div class="fcom_md_tooltip" ref="divRef">
        <button class="toolbar-item" @mousedown="toggleBold">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path
                    d="M8.85758 18.625C8.4358 18.625 8.07715 18.4772 7.78163 18.1817C7.48613 17.8862 7.33838 17.5275 7.33838 17.1058V6.8942C7.33838 6.47242 7.48613 6.11377 7.78163 5.81825C8.07715 5.52275 8.4358 5.375 8.85758 5.375H12.1999C13.2191 5.375 14.1406 5.69231 14.9643 6.32693C15.788 6.96154 16.1999 7.81603 16.1999 8.89038C16.1999 9.63779 16.0194 10.2471 15.6585 10.7183C15.2976 11.1894 14.9088 11.5314 14.4922 11.7442C15.005 11.9211 15.4947 12.2708 15.9614 12.7933C16.428 13.3157 16.6614 14.0192 16.6614 14.9038C16.6614 16.182 16.1902 17.1217 15.2479 17.723C14.3056 18.3243 13.3563 18.625 12.3999 18.625H8.85758ZM9.4883 16.6327H12.3191C13.1063 16.6327 13.6627 16.4141 13.9884 15.9769C14.314 15.5397 14.4768 15.1205 14.4768 14.7192C14.4768 14.3179 14.314 13.8987 13.9884 13.4615C13.6627 13.0243 13.0909 12.8057 12.273 12.8057H9.4883V16.6327ZM9.4883 10.875H12.0826C12.6903 10.875 13.172 10.7013 13.5278 10.3539C13.8836 10.0064 14.0615 9.59037 14.0615 9.10575C14.0615 8.59035 13.8733 8.16918 13.497 7.84225C13.1207 7.51533 12.6595 7.35188 12.1133 7.35188H9.4883V10.875Z"></path>
            </svg>
        </button>
        <button class="toolbar-item" @mousedown="toggleItalic">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path
                    d="M6.29811 18.625C6.04505 18.625 5.83115 18.5375 5.65641 18.3626C5.48166 18.1877 5.39429 17.9736 5.39429 17.7203C5.39429 17.467 5.48166 17.2532 5.65641 17.0788C5.83115 16.9045 6.04505 16.8173 6.29811 16.8173H9.21159L12.452 7.18265H9.53851C9.28545 7.18265 9.07155 7.0952 8.89681 6.9203C8.72206 6.7454 8.63469 6.5313 8.63469 6.278C8.63469 6.02472 8.72206 5.81089 8.89681 5.63652C9.07155 5.46217 9.28545 5.375 9.53851 5.375H16.8847C17.1377 5.375 17.3516 5.46245 17.5264 5.63735C17.7011 5.81225 17.7885 6.02634 17.7885 6.27962C17.7885 6.53293 17.7011 6.74676 17.5264 6.92113C17.3516 7.09548 17.1377 7.18265 16.8847 7.18265H14.2789L11.0385 16.8173H13.6443C13.8973 16.8173 14.1112 16.9048 14.286 17.0797C14.4607 17.2546 14.5481 17.4687 14.5481 17.722C14.5481 17.9752 14.4607 18.1891 14.286 18.3634C14.1112 18.5378 13.8973 18.625 13.6443 18.625H6.29811Z"></path>
            </svg>
        </button>
    </div>
</template>

<style lang="scss">
.fcom_md_tooltip {
    position: absolute;
    display: flex;
    background: #f8f9ff;
    box-shadow: 0px 1px 3px 1px rgba(0, 0, 0, .15), 0px 1px 2px 0px rgba(0, 0, 0, .3);
    border-radius: 8px;
    overflow: hidden;

    button.toolbar-item {
        all: unset;
        width: 32px;
        height: 32px;
        margin: 0;
        padding: 4px;
        cursor: pointer;
        border-radius: 4px;
        border: none;
        background: none;
        box-shadow: none;
        display: flex;
        align-items: center;
        justify-content: center;

        &:hover {
            background: #e9e9e9;
        }

        &:active {
            background: #d9d9d9;
        }
    }
    &[data-show=false] {
        display: none;
    }
}
</style>
