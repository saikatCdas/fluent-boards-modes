import type { Editor } from '@milkdown/kit/core'
import type { PlaceHolderFeatureConfig } from './placeholder'
import type { CodeMirrorFeatureConfig } from './code-mirror'
import type { BlockEditFeatureConfig } from './block-edit'
import type { CursorFeatureConfig } from './cursor'
import type { LinkTooltipFeatureConfig } from './link-tooltip'
import type { ToolbarFeatureConfig } from './toolbar'

export enum CrepeFeature {
  CodeMirror = 'code-mirror',
  LinkTooltip = 'link-tooltip',
  Cursor = 'cursor',
  BlockEdit = 'block-edit',
  Toolbar = 'toolbar',
  Placeholder = 'placeholder'
}

export interface CrepeFeatureConfig {
  [CrepeFeature.Cursor]?: CursorFeatureConfig
  [CrepeFeature.LinkTooltip]?: LinkTooltipFeatureConfig
  [CrepeFeature.BlockEdit]?: BlockEditFeatureConfig
  [CrepeFeature.Placeholder]?: PlaceHolderFeatureConfig
  [CrepeFeature.Toolbar]?: ToolbarFeatureConfig
  [CrepeFeature.CodeMirror]?: CodeMirrorFeatureConfig
}

export const defaultFeatures: Record<CrepeFeature, boolean> = {
  [CrepeFeature.Cursor]: true,
  [CrepeFeature.LinkTooltip]: true,
  [CrepeFeature.BlockEdit]: true,
  [CrepeFeature.Placeholder]: true,
  [CrepeFeature.Toolbar]: true,
  [CrepeFeature.CodeMirror]: true,
}

export async function loadFeature(feature: CrepeFeature, editor: Editor, config?: never) {
  switch (feature) {
    case CrepeFeature.LinkTooltip: {
      const { defineFeature } = await import('./link-tooltip')
      return defineFeature(editor, config)
    }
    case CrepeFeature.BlockEdit: {
      const { defineFeature } = await import('./block-edit')
      return defineFeature(editor, config)
    }
    case CrepeFeature.Placeholder: {
      const { defineFeature } = await import('./placeholder')
      return defineFeature(editor, config)
    }
    case CrepeFeature.Toolbar: {
      const { defineFeature } = await import('./toolbar')
      return defineFeature(editor, config)
    }
  }
}
