import type {DefaultValue} from '@milkdown/kit/core'
import {Editor, defaultValueCtx, editorViewOptionsCtx, rootCtx} from '@milkdown/kit/core'

import {commonmark} from '@milkdown/kit/preset/commonmark'
import {gfm} from '@milkdown/kit/preset/gfm'
import {history} from '@milkdown/kit/plugin/history'
import {getMarkdown} from '@milkdown/kit/utils'
import {clipboard} from '@milkdown/kit/plugin/clipboard'
import {trailing} from '@milkdown/kit/plugin/trailing'

import type {CrepeFeatureConfig} from '../feature'
import {CrepeFeature, defaultFeatures, loadFeature} from '../feature'
import {configureFeatures} from './slice'
import {listener, listenerCtx} from '@milkdown/kit/plugin/listener';

import { upload, uploadConfig, Uploader } from '@milkdown/kit/plugin/upload';

export interface CrepeConfig {
    features?: Partial<Record<CrepeFeature, boolean>>
    featureConfigs?: CrepeFeatureConfig
    root?: Node | string | null
    defaultValue?: DefaultValue,
    onUpdate: (markdown: string) => void,
    handleCommonActions?: (action, ctx) => void
}

export class Crepe {
    static Feature = CrepeFeature
    readonly #editor: Editor
    readonly #initPromise: Promise<unknown>
    readonly #rootElement: Node
    #editable = true

    constructor({
                    root,
                    features = {},
                    featureConfigs = {},
                    defaultValue = '',
                    onUpdate = (markdown: string) => {
                    },
                    handleCommonActions = (action, ctx) => {},
                }: CrepeConfig) {
        const enabledFeatures = Object
            .entries({
                ...defaultFeatures,
                ...features,
            })
            .filter(([, enabled]) => enabled)
            .map(([feature]) => feature as CrepeFeature)

        this.#rootElement = (typeof root === 'string' ? document.querySelector(root) : root) ?? document.body
        this.#editor = Editor.make()
            .config(configureFeatures(enabledFeatures))
            .config((ctx) => {
                ctx.set(rootCtx, this.#rootElement)
                ctx.set(defaultValueCtx, defaultValue)
                ctx.set(editorViewOptionsCtx, {
                    editable: () => this.#editable,
                });
            })
            .config(ctx => {
                const uploader = async (files, schema) => {
                    let fileNodes = await handleCommonActions('handleFileUpload', {
                        ctx,
                        files,
                        schema
                    });

                    return fileNodes;
                };

                ctx.update(uploadConfig.key, (prev) => ({
                    ...prev,
                    uploader,
                }));
            })
            .config((ctx) => {
                const listener = ctx.get(listenerCtx);
                listener.markdownUpdated((ctx, markdown, prevMarkdown) => {
                    if (markdown !== prevMarkdown) {
                        onUpdate(markdown);
                    }
                });

                listener.updated((ctx, node, prevNode) => {
                    handleCommonActions('editor_updated', ctx);
                });
            })
            .use(commonmark)
            .use(history)
            .use(trailing)
            .use(clipboard)
            .use(listener)
            .use(upload)
            .use(gfm)

        const promiseList: Promise<unknown>[] = []

        enabledFeatures.forEach((feature) => {
            const config = (featureConfigs as Partial<Record<CrepeFeature, never>>)[feature]
            promiseList.push(
                loadFeature(feature, this.#editor, config),
            )
        })

        this.#initPromise = Promise.all(promiseList)
    }

    async create() {
        await this.#initPromise
        return this.#editor.create()
    }

    async destroy() {
        await this.#initPromise
        return this.#editor.destroy()
    }

    get editor(): Editor {
        return this.#editor
    }

    setReadonly(value: boolean) {
        this.#editable = !value
        return this
    }

    getMarkdown() {
        return this.#editor.action(getMarkdown())
    }

    getEditor() {
        return this.#editor
    }

    destroyEditor() {
        this.destroy();
    }
}
