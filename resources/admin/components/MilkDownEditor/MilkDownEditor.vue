<script lang="ts">
import { Milkdown, useEditor } from '@milkdown/vue';
import { defaultValueCtx, Editor, rootCtx } from '@milkdown/kit/core';
import { nord } from '@milkdown/theme-nord'
import { tooltipFactory } from '@milkdown/kit/plugin/tooltip';
import { commonmark } from '@milkdown/kit/preset/commonmark'
import { listener, listenerCtx } from '@milkdown/kit/plugin/listener';
import { usePluginViewFactory } from '@prosemirror-adapter/vue';
import Tooltip from './Tooltip.vue';

const tooltip = tooltipFactory('Text');

export default {
    name: 'MilkdownEditor',
    props: {
        markdown: {
            type: String,
            default: ''
        },
        placeholder: {
            type: String,
            default: ''
        }
    },
    emits: ['updateValue'],
    data() {
        return {
            editor: null,
            rootElement: null,
            hasValue: false,
            isOffFocus: false
        }
    },
    components: {
        Milkdown
    },
    methods: {
        initEditor() {
            useEditor((root) => {
                const pluginViewFactory = usePluginViewFactory();
                this.rootElement = root;
                this.editor = Editor.make()
                    .config(nord)
                    .config((ctx) => {
                        ctx.set(rootCtx, root);
                        ctx.set(defaultValueCtx, this.markdown);
                        ctx.set(tooltip.key, {
                            view: pluginViewFactory({
                                component: Tooltip
                            }),
                        });
                    })
                    .config((ctx) => {
                        const listener = ctx.get(listenerCtx);
                        listener.markdownUpdated((ctx, markdown, prevMarkdown) => {
                            if (markdown !== prevMarkdown) {
                                this.$emit('updateValue', markdown);
                            }
                            this.hasValue = !!markdown;
                        });

                        listener.mounted((ctx) => {
                            this.hasValue = !!this.markdown;
                            if (root.querySelector('.milkdown-theme-nord')) {
                                root.querySelector('.milkdown-theme-nord').focus();
                            }
                        });

                        listener.focus(() => {
                            this.isOffFocus = false;
                        });

                        listener.blur(() => {
                            this.isOffFocus = true;
                        });
                    })
                    .use(commonmark)
                    .use(listener)
                    .use(tooltip);

                return this.editor;
            });
        },
        focusOnEditor() {
            if (this.rootElement && this.rootElement.querySelector('.milkdown-theme-nord')) {
                this.rootElement.querySelector('.milkdown-theme-nord').focus();
            }
        }
    },
    created() {
        this.initEditor();
    },
    beforeUnmount() {
        if (this.editor) {
            this.editor.destroy();
        }
    }
}
</script>

<template>
    <div class="fcom_md_seditor_wrapper" :class="{ editor_has_value: hasValue, editor_is_offfocus: isOffFocus }">
        <Milkdown />
        <div @click="focusOnEditor" class="editor-placeholder" v-if="!hasValue && isOffFocus">
            {{ placeholder || $t('Type your message here!') }}
        </div>
    </div>
</template>

<style lang="scss">
.milkdown-theme-nord {
    min-height: 68px;
}
.fcom_md_seditor_wrapper {
    position: relative;
    .editor-placeholder {
        position: absolute;
        top: 0;
        color: #a5a9ad;
        z-index: 0;
    }
}
</style>
