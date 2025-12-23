import { createHash } from 'crypto'
import { writeFileSync } from 'fs'
import { resolve } from 'path'
import { defineConfig } from 'vite'
import terser from '@rollup/plugin-terser'

export default defineConfig({
  build: {
    minify: false,
    lib: {
      entry: resolve(__dirname, 'resources/js/tallkit.js'),
      name: 'TALLKit',
    },
    rollupOptions: {
      output: [
        {
          dir: 'dist',
          entryFileNames: 'tallkit.js',
          format: 'umd',
          name: 'TALLKit',
        },
        {
          dir: 'dist',
          entryFileNames: 'tallkit.min.js',
          format: 'umd',
          name: 'TALLKit',
          plugins: [terser()],
        }
      ]
    },
  },
  plugins: [
    {
      name: 'manifest',
      closeBundle: () => {
        const hash = createHash('md5').update(Date.now().toString()).digest('hex').substr(0, 8)
        writeFileSync('./dist/manifest.json', JSON.stringify({ '/tallkit.js': hash }, null, 2))
      }
    },
  ]
})
