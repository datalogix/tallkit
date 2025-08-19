import { createHash } from 'crypto'
import { writeFileSync } from 'fs'
import { resolve } from 'path'
import { defineConfig } from 'vite'

export default defineConfig({
  build: {
    lib: {
      entry: resolve(__dirname, 'resources/js/tallkit.js'),
      name: 'TALLKit',
      fileName: () => 'tallkit.js',
      formats: ['umd']
    }
  },
  plugins: [
    {
      name: 'manifest',
      closeBundle: () => {
        const hash = createHash('md5').update(Date.now().toString()).digest('hex').substr(0, 8);
        writeFileSync('./dist/manifest.json', JSON.stringify({ '/tallkit.js': hash }, null, 2));
      }
    },
  ]
})
