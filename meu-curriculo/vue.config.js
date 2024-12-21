const { defineConfig } = require('@vue/cli-service')

module.exports = defineConfig({
  publicPath: process.env.NODE_ENV === 'production' ? '/NOME_DO_REPOSITORIO/' : '/',
  transpileDependencies: true
})