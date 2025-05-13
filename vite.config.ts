import { resolve } from 'path'
import { defineConfig } from 'vite'

export default defineConfig({
  build: {
    manifest: 'manifest.json',
    sourcemap: true,
    lib: {
      entry: resolve(__dirname, 'resources/ts/tallkit.ts'),
      name: 'TALLKit',
      fileName: () => 'tallkit.js',
      formats: ['umd']
    }
  }
})
