import { createHash } from 'crypto'
import { readFileSync, writeFileSync } from 'fs'
import { resolve } from 'path'
import { defineConfig } from 'vite'
import terser from '@rollup/plugin-terser'

export default defineConfig(({ mode }) => mode === 'esm'
  ? {
    build: {
      minify: false,
      emptyOutDir: false,
      lib: {
        entry: resolve(import.meta.dirname, 'resources/js/esm.js'),
        formats: ['es'],
        fileName: () => 'tallkit.esm.js',
      },
    },
  }
  : {
    build: {
      minify: false,
      emptyOutDir: false,
      lib: {
        entry: resolve(import.meta.dirname, 'resources/js/tallkit.js'),
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
          const hash = createHash('md5')
            .update(readFileSync('./dist/tallkit.js'))
            .update(readFileSync('./dist/tallkit.min.js'))
            .digest('hex')
            .slice(0, 8)

          writeFileSync('./dist/manifest.json', JSON.stringify({ '/tallkit.js': hash }, null, 2))
        }
      },
    ]
  })
