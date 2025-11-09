# phpcfdi/cfditopdf dockerfile helper

```shell script
# get the project repository on folder "cfditopdf"
git clone https://github.com/phpcfdi/cfditopdf.git cfditopdf

# build the image "cfditopdf" from folder "cfditopdf/"
docker build --tag cfditopdf cfditopdf/

# remove image cfditopdf
docker rmi cfditopdf
```

## Run command

The project is installed on `/opt/source/` and the entry point is the command
`/usr/local/bin/php /opt/source/bin/cfditopdf`.

```shell
# show help
docker run -it --rm --user="$(id -u):$(id -g)" cfditopdf --help

# montar un volumen para ejecutar una conversión
docker run -it --rm --user="$(id -u):$(id -g)" --volume="${PWD}/files:/files" \
  cfditopdf /files/cfdis/F-12345.xml /files/pdfs/F-12345.pdf
```
