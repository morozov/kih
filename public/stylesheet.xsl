<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
    <xsl:template match="/">
        <xsl:text disable-output-escaping='yes'>&lt;!DOCTYPE html&gt;</xsl:text>
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <title><xsl:value-of select="/rss/channel/title"/> RSS Feed</title>
                <meta http-equiv="content-type" content="text/html; charset=utf-8" />
                <style type="text/css">
                    body {
                        font-family: Helvetica, Arial, sans-serif;
                        font-size: 14px;
                        color: #545454;
                        background: #E5E5E5;
                        line-height: 1.5;
                    }
                    h2, a, a:link, a:visited {
                        color: #005C82;
                        text-decoration: none;
                    }
                    a:hover {
                        color: #000;
                    }
                    h1, h2, h3, p {
                        margin-top: 0;
                        margin-bottom: 20px;
                    }
                    h3 {
                        font-style: italic;
                    }
                    #content {
                        width: 700px;
                        margin: 0 auto;
                        background: #FFF;
                        padding: 30px;
                        border-radius: 1em;
                        box-shadow: 0 0 2px #5D5D5D;
                    }
                    #channel-image {
                        float: right;
                        width: 200px;
                        margin-bottom: 20px;
                    }
                    #channel-image img {
                        width: 200px;
                        height: auto;
                        border-radius: 5px;
                    }
                    #channel-header {
                        margin-bottom: 20px;
                    }
                    .channel-item {
                        clear: both;
                        border-top: 1px solid #E5E5E5;
                        padding: 20px;
                    }
                    .episode-image img {
                        width: 100px;
                        height: auto;
                        margin: 0 30px 15px 0;
                        border-radius: 5px;
                    }
                    .episode_meta {
                        font-size: 11px;
                        font-weight: bold;
                    }
                </style>
            </head>
            <body>
                <div id="content">
                    <div id="channel-header">
                        <h1>
                            <xsl:if test="/rss/channel/image">
                                <div id="channel-image">
                                    <a>
                                        <xsl:attribute name="href">
                                            <xsl:value-of select="/rss/channel/image/link"/>
                                        </xsl:attribute>
                                        <img>
                                            <xsl:attribute name="src">
                                                <xsl:value-of select="/rss/channel/image/url"/>
                                            </xsl:attribute>
                                            <xsl:attribute name="title">
                                                <xsl:value-of select="/rss/channel/image/title"/>
                                            </xsl:attribute>
                                        </img>
                                    </a>
                                </div>
                            </xsl:if>
                            <xsl:value-of select="/rss/channel/title"/>
                        </h1>
                        <p>
                            <xsl:value-of select="/rss/channel/description"/>
                        </p>
                    </div>
                    <xsl:for-each select="/rss/channel/item">
                        <div class="channel-item">
                            <h2>
                                <xsl:value-of select="title"/>
                            </h2>
                            <xsl:if test="description">
                                <p>
                                    <xsl:value-of select="description" disable-output-escaping="yes"/>
                                </p>
                            </xsl:if>
                            <p class="episode_meta">
                                <audio controls="controls" preload="none">
                                    <xsl:attribute name="src">
                                        <xsl:value-of select="enclosure/@url"/>
                                    </xsl:attribute>
                                    <a>
                                        <xsl:attribute name="href">
                                            <xsl:value-of select="enclosure/@url"/>
                                        </xsl:attribute>
                                        Play episode
                                    </a>
                                </audio>
                            </p>
                        </div>
                    </xsl:for-each>
                </div>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
